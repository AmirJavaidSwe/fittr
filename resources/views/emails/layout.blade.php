<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <title>{{ config('app.name') }}</title>
    <!--[if gte mso 15]>
    <xml>
        <o:OfficeDocumentSettings>
        <o:AllowPNG/>
        <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>
</head>
<body>
    <!--[if mso]>
    <style type="text/css">
        body, table, td, p, h1, h2, h3, span, a { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; }
    </style>
    <![endif]-->
    <!--[if mso]>
    <center>
    <table><tr><td width="600">
    <![endif]-->
    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        @include('emails.partials.header', ['business_settings' => $business_settings])
                    </tr>
                    <!-- Email Body -->
                    <tr>
                        <td class="body" width="100%" cellpadding="0" cellspacing="0" style="border: hidden !important;">
                            <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                                <!-- Body content -->
                                <tr>
                                    <td class="content-cell">
                                        {!! $template['content'] !!}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center">
                            <table width="570" cellpadding="0" cellspacing="0" style="border: hidden !important;">
                                <tr><td height="50"></td></tr>
                                <tr><td class="line"></td></tr>
                                <tr><td height="50"></td></tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        @include('emails.partials.footer', [
                            'business_settings' => $business_settings,
                            'template' => $template,
                        ])
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <!--[if mso]>
    </td></tr></table>
    </center>
    <![endif]-->
</body>
</html>