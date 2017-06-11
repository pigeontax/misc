<?php
/*
 * https://stackoverflow.com/questions/7853467/uploading-a-file-in-chunks-using-html5
 */

$target_path = "uploads/";
$tmp_name = $_FILES['fileToUpload']['tmp_name'];
$size = $_FILES['fileToUpload']['size'];
//get name from post if passed in, as sliced name will always be blob
$name = isset($_POST['fileName']) ? $_POST['fileName'] : $_FILES['fileToUpload']['name'];
$step = isset($_POST['step']) ? $_POST['step'] : 0;



if(isset($_POST['humpty'])) {
    //if humpty flag is set, then it's time to put the file together

    $target_file = $target_path . basename($name);
    $ctr = 0;

    while ($ctr < $step) {
        //create the file if new, or set for appending if it already exists
        if(file_exists($target_file)) {
            $out = fopen($target_file, "ab");
        }else {
            $out = fopen($target_file, "wb");
        }

        if ($out) {
            // Read binary input stream and append it to temp file
            $in = fopen($target_file."_$ctr", 'rb');
            if ($in) {
                while ($buff = fread($in, 1048576)) {
                    fwrite($out, $buff);
                    fwrite($compare, $buff);
                }
            }
            fclose($in);
            fclose($out);

            //remove the sliced file from filesystem
            unlink($target_file."_$ctr");
        }
        $ctr++;
    }


} else {
    //create a sliced file in the system for later processing
   $target_file = $target_path . basename($name). '_'.$step;
   $out = fopen($target_file, "wb");

    if ($out) {
        // Read binary input stream and append it to temp file
        $in = fopen($tmp_name, "rb");
        if ($in) {
            while ($buff = fread($in, 1048576)) {
                fwrite($out, $buff);
                fwrite($com, $buff);
            }
        }
        fclose($in);
        fclose($out);
    }
}

?>
