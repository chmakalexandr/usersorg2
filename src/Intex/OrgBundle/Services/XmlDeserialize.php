<?php

namespace Intex\OrgBundle\Services;

use Exception;


class XmlDeserialize
{
    private $serializer;

    public function __construct($serializer)
    {
        $this->serializer = $serializer;
    }

    public function deserializeCompanies($xmlFile)
    {
        try {
            if ((!$xmlFile)||$xmlFile['file']->getError()){
                throw new Exception($this->get('translator')->trans('Error load file. Please check uploaded file'));
            }

            $xmlData = file_get_contents($xmlFile['file']->getRealPath());
            $data = $this->serializer->deserialize($xmlData, 'Intex\OrgBundle\Entity\Organizations', 'xml');

            return $data->getCompanies();
        } catch (Exception $e) {
            throw new Exception($this->get('translator')->trans('Unnable add users in Db. Check XML file.'));
        }
    }
}