<?php

namespace Intex\OrgBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Intex\OrgBundle\Entity\User;
use Intex\OrgBundle\Entity\Company;
use Intex\OrgBundle\Form\UserType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Exception;

/**
 * Class UserController
 * @package Intex\OrgBundle\Controller
 */
class UserController extends Controller
{

    /**
     * Render list all users
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listUsersAction()
    {
        $em = $this->getDoctrine()
            ->getManager();

        $users = $em->getRepository('IntexOrgBundle:User')
            ->findAll();

        return $this->render('IntexOrgBundle:User:index.html.twig', array(
            'users' => $users
        ));
    }

    /**
     * Render information about user by id
     * @param int $userId Id user's
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showUserAction($userId)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('IntexOrgBundle:User')->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find user.');
        }

        return $this->render('IntexOrgBundle:User:show.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * Renders list users of the company
     * @param int $companyId Id organization's
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listOrgUsersAction($companyId)
    {
        $company = $this->getCompany($companyId);
        $users = $company->getUsers();

        if (!$company) {
            throw $this->createNotFoundException('Unable to find company.');
        }

        return $this->render('IntexOrgBundle:User:users.html.twig', array(
            'company' => $company,
            'users' => $users
        ));
    }

    /**
     * Renders form for add user to company
     * @param int $companyId organization's Id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newUserAction($companyId)
    {
        $company = $this->getCompany($companyId);

        if (!$company) {
            throw $this->createNotFoundException('Unable to find company.');
        }

        $user = new User();
        $user->setCompany($company);
        $form = $this->createForm(UserType::class, $user);

        return $this->render('IntexOrgBundle:User:form.html.twig', array(
            'company' => $company,
            'form' => $form->createView()
        ));
    }

    /**
     * Add user in DB
     * @param Request $request
     * @param int $companyId organization's Id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createUserAction(Request $request, $companyId)
    {
        $company = $this->getCompany($companyId);

        if (!$company) {
            throw $this->createNotFoundException('Unable to find company.');
        }

        $user = new User();
        $user->setCompany($company);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $users = $company->getUsers();

            $this->addFlash('success', $this->get('translator')->trans('User was be added!'));
            return $this->redirect($this->generateUrl('intex_org_company_users', array('companyId' => $companyId, 'company' => $company, 'users' => $users)));
        }

        return $this->render('IntexOrgBundle:User:form.html.twig', array(
            'company' => $company,
            'form' => $form->createView()
        ));
    }

    /**
     * Load companies with users in DB from XML file
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loadUsersAction(Request $request)
    {
        try {
            $allUsersPresent = true;
            $em = $this->getDoctrine()->getManager();

            $companies = $this->getCompaniesFromXml($request);
            $existingCompanies = $em->getRepository('Intex\OrgBundle\Entity\Company')->getExistingCompanies($companies);
            $existingOgrns = $em->getRepository('Intex\OrgBundle\Entity\Company')->getOgrns($existingCompanies);

            foreach ($companies as $organization) {
                if (!in_array($organization->getOgrn(), $existingOgrns)) {
                    $company = $this->createNewCompany($organization);
                    $em->persist($company);
                } else {
                    $company = $this->getCompanyByOgrn($organization->getOgrn(), $existingCompanies);
                }

                $users = $organization->getUsers();
                $newUsers = $em->getRepository('Intex\OrgBundle\Entity\User')->getNewUsers($users);
                if (!empty($newUsers)) {
                    foreach ($newUsers as $user) {
                        $user->setCompany($company);
                        $em->persist($user);
                    }
                    $allUsersPresent = false;
                }
            }
            $em->flush();
        } catch (Exception $e) {
            $this->addFlash('error', $this->get('translator')->trans('Unnable add users in Db. Check XML file.'));
            return $this->redirect($this->generateUrl('intex_org_user_upload'));
        }

        if ($allUsersPresent) {
            $this->addFlash('warning', $this->get('translator')->trans('All uploadable users are present in DB'));
        } else {
            $this->addFlash('success', $this->get('translator')->trans('Users successfully loaded'));
        }

        return $this->redirect($this->generateUrl('intex_org_user_upload'));
    }

    /**
     * Renders form for upload users from XML file
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function uploadXmlAction()
    {
        $form = $this->createFormBuilder()
            ->add('file', FileType::class, array('label' => $this->get('translator')->trans('Load XML file'),
                "attr" => array("accept" => ".xml",)))
            ->getForm();

        return $this->render('IntexOrgBundle:User:upload.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Shows the company in which the user belongs
     * @param int $companyId Id organization's
     * @return \Intex\OrgBundle\Entity\Company|null|object
     */
    protected function getCompany($companyId)
    {
        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository('IntexOrgBundle:Company')->find($companyId);

        if (!$company) {
            throw $this->createNotFoundException('Unable to find company.');
        }

        return $company;
    }

    /**
     * Return company from array $companies in which the Primary State Registration Number = $ogrn
     * @param int $ogrn Primary State Registration Number organization's
     * @param array $companies array organizations
     * @return \Intex\OrgBundle\Entity\Company|null|object
     */
    protected function getCompanyByOgrn($ogrn, $companies)
    {
        foreach ($companies as $company) {
            if ($company->getOgrn() == $ogrn) {
                return $company;
            }
        }

        return null;
    }

    /**
     * Return companies from XML file
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    protected function getCompaniesFromXml(Request $request)
    {
        try {
            $xmlFile = $request->files->get('form');

            if ((!$xmlFile)||$xmlFile['file']->getError()){
                throw new Exception($this->get('translator')->trans('Error load file. Please check uploaded file'));
            }

            $xmlData = file_get_contents($xmlFile['file']->getRealPath());
            $data = $this->get('jms_serializer')->deserialize($xmlData, 'Intex\OrgBundle\Entity\Organizations', 'xml');

            return $data->getCompanies();
        } catch (Exception $e) {
            throw new Exception($this->get('translator')->trans('Unnable add users in Db. Check XML file.'));
        }
    }

    protected function createNewCompany($organization){

        $company = new Company();
        $company->setName($organization->getName());
        $company->setOgrn($organization->getOgrn());
        $company->setOktmo($organization->getOktmo());

        return $company;
    }
}
