<?php

function get_home_path_() {
    $home    = set_url_scheme( get_option( 'home' ), 'http' );
    $siteurl = set_url_scheme( get_option( 'siteurl' ), 'http' );
 
    if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) {
        $wp_path_rel_to_home = str_ireplace( $home, '', $siteurl ); /* $siteurl - $home */
        $pos                 = strripos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), trailingslashit( $wp_path_rel_to_home ) );
        $home_path           = substr( $_SERVER['SCRIPT_FILENAME'], 0, $pos );
        $home_path           = trailingslashit( $home_path );
    } else {
        $home_path = ABSPATH;
    }
 
    return str_replace( '\\', '/', $home_path );
}

$n=10;
function getName($n) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
  
    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }
  
    return $randomString;
}

function filemanager_shortcode() { 

    $path = $_GET['path'];

    if ($path == null) {
        $path = get_home_path_();
    }

    $files = scandir($path);
    global $wp;

    if ( (is_dir($path) == true) && (is_user_logged_in()) ) {
        echo "<div id='sequentialupload' class='sequentialupload' data-object-id='$path'></div>"; 
    }
    echo "<div id='filemanager-wrapper' class='filemanager-wrapper'>";
    if ( (is_dir($path) == true) && (is_user_logged_in()) ) {
        ?><script type="text/javascript">document.getElementById("sequentialupload").style.display = "block";</script><?php
        ?><div id='filemanagerbtn'>
                <div class='navbar'>
                    <a class='btndelete'>Delete</a>
                    <div class='subnav'>
                        <button class='subnavbtn btnnewdir'>Create dir</button>
                        <div id='subnav-content' class='subnav-content'>
                            <span>
                                <input type='text' id='lname' name='lname'></input>
                                <button class='newdir'>ok</button>
                            <span>
                        </div>
                    </div>
                    <div class='btnrename'>Rename</div>
                </div>
            </div><?php
    }
    if ( is_dir($path) == true ) {
        echo "<table id='file-table'>";
            foreach($files as $file){
                $pathfilezise = $path.'/'.$file;
                $filesize = filesize($pathfilezise);
                $realpath = realpath($path.'/'.$file);
                if ( is_dir($path.'/'.$file) == true ) {
                    echo "<tr><td><input class='checkbox' type='checkbox' name='$realpath'/></td><td class='filemanager-table'><a id='file-id' class='filemanager-click' href='" . home_url($wp->request) . "/?path=$realpath'>$file</a></td><td>$filesize</td></tr>";
                } else {
                    echo "<tr><td><input class='checkbox' type='checkbox' name='$realpath'/></td><td class='filemanager-table'><a id='file-id' class='filemanager-click' href='" . home_url($wp->request) . "/?path=$realpath'>$file</a></td><td>$filesize</td></tr>";
                }
            }
        echo "</table>";
    } else {
        $object_id = $path;
        $getname = getName(32);
        $target =  $object_id;
        $direname = dirname($object_id);
        $basename = strtolower(basename($object_id));
        $ext = pathinfo($basename, PATHINFO_EXTENSION);

        ?><script type="text/javascript">document.getElementById("sequentialupload").style.display = "none";</script><?php

        $link = get_home_path_() .'/files/'. $getname .'.'. $ext;
        echo exec('mkdir "'. get_home_path_() .'/files"');
        echo exec('rm "'. $link .'"');
        echo exec('ln -s "' . $target . '" "' . $link .'"');

        if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'bmp') {
            echo '<img src="' . get_home_url() . '/files/' . $getname . '.' . $ext . '"></img>';
        } elseif ($ext == 'mp4' || $ext == 'mkv' || $ext == 'avi' ) {
            echo '<div class="file-info">' . basename($object_id) . '</div>';
            echo '<div id="dplayer"></div>';
            ?><script type="text/javascript">
            window.dp1 = new DPlayer({
                container: document.getElementById('dplayer'),
                preload: 'none',
                volume: 0.7,
                screenshot: true,
                video: {
                    url:  '<?php echo get_home_url() . '/files/' . $getname . '.' . $ext ?>',
                    pic:  null,
                    thumbnails: null
                },
                subtitle: {
                    url: null
                }
            });</script><?php
        } elseif ($ext == 'pdf') {
            echo '<div id="pdf"></div>';
            ?><script type="text/javascript">PDFObject.embed('<?php echo get_home_url() . '/files/' . $getname . '.' . $ext ?>', "#pdf");</script><?php
        } else {
            echo '<a href="' . get_home_url() . '/files/' . $getname . '.' . $ext . '">Download</a>';
        }
    }
    echo "</div>";

}
    
add_shortcode('filemanager-shortcode', 'filemanager_shortcode');

?>