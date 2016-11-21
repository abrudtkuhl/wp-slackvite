<?php
    $slackvite = Slackvite::get_instance();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Join <?php the_title(); ?> On Slack - Slackvite</title>
    <meta name="description" value="Join {{$title}} Slack Team. Powered by Slackvite.">

    <link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ ) . 'slackvite.css'; ?>">

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <link rel="shortcut icon" href="https://slack.global.ssl.fastly.net/272a/img/icons/favicon-32.png">
</head>

<body id="team-landing" style="background: url('<?php echo $slackvite->get_background_image(); ?>') no-repeat 50% 50% fixed; background-size: cover;">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="landing-container">
                    <div class="landing-hero">
                        <?php if (count($_POST)>0) : ?>
                            <div class="alert alert-success">
                                <strong>Success! Check your email to confirm</strong>
                            </div>
                        <?php endif; ?>
                        <?php if ( isset( $_POST['slackvite-email'] ) && isset( $slackvite->flash_message ) ) : ?>
                            <div class="alert alert-danger">
                                <strong>Oops, An Error Happened</strong><br />
                                <?php echo $slackvite->flash_message; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                            <h1>Join <strong><?php echo get_option('slackvite_team_api_key'); ?></strong> On Slack</h1>
                            <?php if( strlen(get_the_content()) > 0 ) : ?>
                                <?php the_content(); ?>
                            <?php else: ?>
                                <p>Enter your email address in the form below, click <em>Get Invite</em>, and your invitation will be on its way.</p>
                            <?php endif; ?>
                        <?php endwhile; endif; ?>

                        <form action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" method="post">
                            <div class="row">
                                <div class="col-xs-12 col-sm-9">
                                    <input type="text" name="slackvite-email" id="email" class="form-control input-lg"/>
                                </div>
                                <div class="col-xs-12 col-sm-3">
                                    <input type="submit" value="Signup" class="btn btn-success btn-lg text-uppercase btn-block landing-submit" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer navbar-fixed-bottom" role="footer">
        <a href="https://slackvite.com/?utm_source=wordpress_plugin&utm_medium=landing_page&utm_content=<?php echo $slackvite->get_landing_page_url(); ?>" title="Slack Team Signup Pages">powered by <strong>slackvite</strong></a>
    </footer>

    <!-- JavaScripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-586943-65', 'auto');
      ga('send', 'pageview');

    </script>
</body>
</html>
