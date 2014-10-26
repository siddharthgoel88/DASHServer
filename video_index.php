 <?php
 $dir = "upload/";
        $allDirs = array_diff(scandir($dir), array('..', '.'));
        $videos = array();
        foreach ($allDirs as $onedir) {
            if (is_dir($dir . $onedir)) {
                $videos[] = $onedir;
            }
        }
        print_r($videos);
        ?>