<!DOCTYPE html>
<?php
    if (!$User->authURI($info[0])) {
        header("Location: index.php");
        die();
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

                a.highlighted {
                    color: #ff8a01;
                }

		.btn-active {
                   background-color: #4bf442 !important;
                   color: #000 !important;
                }
                .btn-inactive {
                   background-color: #e21e09 !important;
                }

        </style>
        <head>
                        <!--  Meta  -->
                        <?php require_once 'inc/meta.php'; ?>
                        <title>Library Settings</title>
                        <!--  CSS  -->
                        <?php require_once 'inc/css.php'; ?>
			<!--  get_plex_user  -->
			<?php include 'inc/update_plex_user.php'; ?>
			
			<?php
			$Shares = update_plex_user($User->getUsername(), [], false);
			$FormStart = '<div><form action="/?page=librariessubmit" method="POST">';
			$FormEnd = '</form></div><br>';
			?>


		<h2><u><center>Library Settings</center></u></h2>
		</head>
		<body>
			<main class="valign-wrapper">
				<div class="container valign">
					<div class="section">
						<div class="row">
							<div class="col s12">
								<div>
									<?php
									if ($Shares !== null) {
										echo '<h3>Configure which libraries you would like to see;</h3>';
										foreach ($Shares as $section) {
											if ($section['shared'] == '1') {
												echo $FormStart;
												echo '<input name="librarysetting" type="hidden" value="' . $section['section'] . ':0"  />';
												echo '<button class="btn btn-active" type="submit" name="action"><i class="fa fa-toggle-on"></i>&nbsp;' . $section['title'] . '</button>';
												echo $FormEnd;
											} else {
												echo $FormStart;
												echo '<input name="librarysetting" type="hidden" value="' . $section['section'] . ':1"  />';
												echo '<button class="btn btn-inactive" type="submit" name="action"><i class="fa fa-toggle-off"></i>&nbsp;' . $section['title'] . '</button>';
												echo $FormEnd;
											}
										}
									} else {
										echo '<h3>You cannot modify any libraries.</h3>';
									}
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
           </main>
                <!--  Scripts  -->
                <?php require_once 'inc/javascripts.php'; ?>

                <script>
		$(".btn").click(function () {
		$(this).children().removeClass()
		$(this).children().addClass('fa fa-spinner fa-spin')
		});
		</script>

        </body>
</html>
