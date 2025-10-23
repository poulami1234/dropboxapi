<html>
<head></head>
<body>

    <?php
    // https://www.dropbox.com/oauth2/authorize?client_id=470pnwinqhhtass&redirect_uri=http://localhost/dropbox-api/oauth-step2.php&response_type=code&token_access_type=online


        //https://davescripts.com/php-dropbox-api-oauth-setup-php-scripts
    //https://dropbox.github.io/dropbox-sdk-php/api-docs/v1.0.x/class-Dropbox.Client.html
    $client_id = '470pnwinqhhtass';

    $redirect_url = 'http://localhost/dropbox-api/oauth-step2.php';

    $authorization_url = 'https://www.dropbox.com/oauth2/authorize?client_id=' . $client_id
        . '&token_access_type=offline'
        . '&response_type=code'
        . '&redirect_uri=' . $redirect_url
    ?>
    <div style="text-align:center">
        <p>My Dropbox App</p>
        <a href="<?php echo $authorization_url; ?>">Authorize Dropbox</a>
    </div>
</body>