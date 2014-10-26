<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>CS5248</title>
    </head>
    <body>
        <form enctype="multipart/form-data" action=upload.php method=post> 
            <input type="hidden" name="MAX_FILE_SIZE" value="20000000">
            <input type=file name=upfile size=20>
            <input type=submit value='Upload File'> 
        </form> 
        <?php
        $dir = "upload/";

        if (!is_dir($dir)) {
            echo "this is not a dir for " . $dir . "<br>";
            if (mkdir($dir, 0777)) {
                echo "mkdir success!<br>";
            } else {
                echo "mkdir failed!<br>";
            }
        }
//        system('chcon -R -t httpd_sys_rw_content_t ' . $dir, $retVal;
        ?>
        <!-- List of Videos -->
        <?php
        $allDirs = array_diff(scandir($dir), array('..', '.'));
        $videos = array();
        foreach ($allDirs as $onedir) {
            if (is_dir($dir . $onedir)) {
                $videos[] = $onedir;
            }
        }
        ?>
        <style>
            table, th, td {
                border: 1px solid black;
                border-collapse: collapse;
            }
        </style>
        <table style="width:100%">
            <tr>
                <th style="width:20%">Name</th>
                <th style="width:80%">Content</th>
            </tr>
            <?php foreach ($videos as $video): ?>
                <?php $videoChildren = array_diff(scandir($dir . $video), array('..', '.')); ?>
                <?php if (count($videoChildren) > 0): ?>
                    <tr>
                        <td rowspan="<?php echo max(count($videoChildren), 1); ?>"><?php echo $video; ?></td>
                        <td><?php echo $videoChildren[2]; ?></td>
                    </tr>
                    <?php foreach ($videoChildren as $index => $videoChild): ?>
                        <?php if ($index !== 2): ?>
                            <tr>
                                <td><?php echo $videoChild; ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <td><?php echo $video; ?></td>
                    <td>No Content</td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>