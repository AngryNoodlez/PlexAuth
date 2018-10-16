<!DOCTYPE html>
<?php

	include '/inc/update_plex_user.php';

    if (!$User->authURI($info[0])) {
        header("Location: index.php");
        die();
    }

    $Username = $User->getUsername();
    $librarysetting = $_POST['librarysetting'];

    $shares = update_plex_user($Username, [], false);
    
    $args = explode(":", $librarysetting);

    $section = $args[0];
    $status = $args[1];

      

    if($status == '1') {
        foreach ($shares as $sections) {
            if ($sections['shared'] !== '0') {
                $CurrentShares .= $sections['section'] . ', ';
            }
        }
        $NewShares = $CurrentShares . $section;
        $NewShares = explode(', ', $NewShares);

	update_plex_user($Username, $NewShares, false);

    } else {
        foreach ($shares as $sections) {
            if ($sections['shared'] !== '0' && $sections['section'] !== $section) {
                $NewShares .= $sections['section'] . ', ';
            }
        }

        $NewShares = rtrim($NewShares, ', ');
        $NewShares = explode(', ', $NewShares);


//	if(empty($NewShares['0'])) {
//        update_plex_user($Username, [], true);
//        } else {
//        update_plex_user($Username, $NewShares, false);
//        }

        update_plex_user($Username, $NewShares, false);
   }

?>

<html lang="en">
        <style>

                body {
                    background-color: #1F1F1F;
                }

                body h1 {
                    color: #ff8a01;
                    font-size: 40px;
                }

                body h2 {
                    color: #ff8a01;
                    font-size: 30px;
                }

                body h3 {
                    color: #fff;
                    font-size: 20px;
                }

                body p {
                    color: #ff8a01;
                    font-size: 14px;
                }

                body table {
                    border-collapse: collapse !important;
                    width: 100% !important;
                }

                th {
                    background-color: #ff8a01;
                    color: white;
                    text-align: center !important;
                }

                body table, td {
                    color: #ff8a01 !important;
                    font-size: 14px !important;
                    border: 1px solid #ff8a01 !important;
                    width: 50%;
                }

                a.highlighted {
                    color: #ff8a01;
                }

        </style>
        <head>
                <!--  Meta  -->
                <?php require_once 'inc/meta.php'; ?>
                <title>Library Settings</title>
                <!--  CSS  -->
                <?php require_once 'inc/css.php'; ?>
                <h2><u><center>Library Settings</center></u></h2>
                <br>
				
		<?php
		   echo '<h3><center>You should be redirected in a moment..</center></h3>';
           echo '<center><button class="btn plex-orange" onclick="window.location.href=\'/?page=libraries\'" name="goback"><i class="fa fa-spinner fa-spin"></i>&nbsp;Applying...</center>';
		?>

        <body>

                <!--  Scripts  -->
                <?php require_once 'inc/javascripts.php'; ?>
		<script>
                $(".btn").click(function () {
                $(this).children().removeClass()
                $(this).children().addClass('fa fa-spinner fa-spin')
                });
                </script>

		<!--   Redirect Back -->
		<meta http-equiv="refresh" content="0;URL=/?page=libraries" />
        </body>
</html>
