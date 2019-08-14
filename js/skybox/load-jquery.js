/* Skybox - Load jQuery */

if ((typeof jQuery === 'undefined') && !window.jQuery) {
    //document.write('<scr' + 'ipt type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></scr' + 'ipt>');
    document.write('<scr' + 'ipt type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></scr' + 'ipt>');
    document.write('<scr' + 'ipt type="text/javascript"> jQuery.noConflict(); </scr' + 'ipt>');
    //document.write(unescape("%3Cscript type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'%3E%3C/script%3E"));
    //document.write(unescape("%3Cscript type='text/javascript'%3E%3C/script%3E jQuery.noConflict(); %3Cscript%3E%3C/script%3E"));
}