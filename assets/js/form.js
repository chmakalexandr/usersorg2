require('moment');
require('../dist/bootstrap/bootstrap-datepicker/js/bootstrap-datepicker.min');

$(document).ready(function() {
    $("#intex_orgbundle_usertype_bithday").datepicker({format: "yyyy-mm-dd", startView: 1});
});
