/*Check file size*/
const MAX_FILE_SIZE = 2097152; //2mb

var tr = require('./translate.js');

var str = location.href;
var locale = 'en_US'; //location
if (str.match('\\/ru\\/')){
    locale = 'ru_RU';
}

$(document).ready(function() {
    $('#up_submit').click( function() {
        //check whether browser fully supports all File API
        if (window.File && window.FileReader && window.FileList && window.Blob)
        {
            //get the file size and file type from file input field
            var fsize = $('#form_file')[0].files[0].size;
            if(fsize > MAX_FILE_SIZE) //do something if file size more than MAX_FILE_SIZE
            {
               //1mb = 1048576
               alert((fsize/1048576).toFixed(1) + tr.trans('error:file:toobig', locale)+" "+(MAX_FILE_SIZE/1048576).toFixed(1)+" Mb");
               return false;
            } else if (fsize == 0){
                alert(tr.trans('error:file:blank', locale));
                return false;
            }

            //check file extension
            var file_name = $('#form_file')[0].files[0].name;
            var file_type = file_name.split('.').pop().toLowerCase();
            if(file_type != "xml"){
                alert(tr.trans('error:file:xmlfile', locale));
                return false;
            }
        } else {
            alert(tr.trans('error:file:upgrade', locale));
        }
        return true;
    });
});