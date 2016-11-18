<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
<div style="margin:5px 0 0 0;padding:0;background-color:#f3f3f3" bgcolor="#f3f3f3">
        <table style="color:#4a4a4a;font-family:'Museo Sans Rounded',Museo Sans Rounded,'Museo Sans',Museo Sans,'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:14px;line-height:20px;border-collapse:callapse;border-spacing:0;margin:0 auto" bgcolor="#f3f3f3" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tbody><tr>
            
                <td style="padding-left:10px;padding-right:10px">
                    <table style="width:100%;margin:0 auto;max-width:600px" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tbody>
                        <tr>
                            <td style="text-align:center;padding-top:3%">
                                <a href="http://www.zombilingo.org" style="text-decoration:none" target="_blank">
                                    <img src="http://www.zombilingo.org/img/email/header-email.png" style="display:block;margin:0;border:0;max-width:600px" width="100%">
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <td style="background-color:#ffffff;padding:9% 9% 2% 9%" bgcolor="#ffffff">
                                    <table style="width:100%;padding-bottom:20px" border="0" cellpadding="0" cellspacing="0">
                                        <tbody>
                                        <tr>
                                            <td style="padding-right:5%" width="100%">

                                                    @yield('content')

                                            </td>
                                    </tr></tbody></table>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align:center;">
                                <a href="http://www.zombilingo.org" style="text-decoration:none" target="_blank">
                                    <img src="http://www.zombilingo.org/img/email/footer-email.png" style="display:block;margin:0;border:0;max-width:600px" width="100%">
                                </a>
                            </td>
                        </tr>                        
                        <tr>
                            <td style="font-size:0px">&nbsp;</td>
                        </tr>

                        <tr>
                            <td border="0" cellspacing="0" cellpadding="0" style="padding:20px 0" width="100%">
                                <table style="color:#808080;font-size:14px;line-height:18px;border-collapse:collapse" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tbody><tr>
                                        <td dir="ltr" style="padding-bottom:5px;padding-right:5px" width="50%">
                                            <table dir="ltr" style="line-height:22px;height:24px;margin:0 0 10px auto" align="right" border="0" cellpadding="0" cellspacing="0">
                                                <tbody><tr>
                                                    <td><p style="margin:0"><a href="http://twitter.com/zombilingo" target="_blank"><img src="http://www.zombilingo.org/img/email/corner-left.png" style="width:14px;min-height:28px;display:block;border:0" height="28" width="14"></a></p></td>
                                                    <td style="height:26px;color:white;border-top:1px solid #bbbbbb;border-bottom:1px solid #bbbbbb;display:block" height="26" valign="middle">
                                                        <a style="color:#bbbbbb;font-weight:700;text-decoration:none;padding:0;font-size:10px" href="http://twitter.com/zombilingo" target="_blank"><img src="http://www.zombilingo.org/img/email/twitter.png" style="border:0;white-space:nowrap;width:14px;display:inline;vertical-align:middle" width="14"><span style="padding-left:8px">Nous suivre</span></a>
                                                    </td>
                                                    <td><p style="margin:0"><a href="http://twitter.com/zombilingo" target="_blank"><img src="http://www.zombilingo.org/img/email/corner-right.png" style="width:14px;min-height:28px;display:block;border:0" height="28" width="14"></a></p></td>
                                                </tr>
                                            </tbody></table>
                                        </td>
                                        <td dir="ltr" style="padding-bottom:5px;padding-left:5px" width="50%">
                                            <table style="line-height:22px;height:24px;margin:0 auto 10px 0" align="left" border="0" cellpadding="0" cellspacing="0">
                                                <tbody><tr>
                                                    <td><p style="margin:0"><a href="http://www.facebook.com/zombilingo" target="_blank"><img src="http://www.zombilingo.org/img/email/corner-left.png" style="width:14px;min-height:28px;display:block;border:0" height="28" width="14"></a></p></td>
                                                    <td style="height:26px;color:white;border-top:1px solid #bbbbbb;border-bottom:1px solid #bbbbbb;display:block" height="26" valign="middle">
                                                        <a style="color:#bbbbbb;font-weight:700;text-decoration:none;padding:0;font-size:10px" href="http://www.facebook.com/zombilingo" target="_blank"><img src="http://www.zombilingo.org/img/email/facebook.png" style="border:0;width:6px;display:inline;vertical-align:middle" width="6"><span style="padding-left:8px">J'aime</span></a>
                                                    </td>
                                                    <td><p style="margin:0"><a href="http://www.facebook.com/zombilingo" target="_blank"><img src="http://www.zombilingo.org/img/email/corner-right.png" style="width:14px;min-height:28px;display:block;border:0" height="28" width="14"></a></p></td>
                                                </tr>
                                            </tbody></table>
                                        </td>
                                    </tr>
                                </tbody></table>
                                <table style="border-collapse:collapse" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tbody><tr>
                                        <td style="padding:20px" width="100%">
                                            <div style="margin:0;text-align:center;font-size:12px;color:#bbbbbb">Si tu ne souhaites pas recevoir de notifications, tu peux te désabonner <a href="http://www.zombilingo.org/auth/unsubscribe?email=<?php echo $user->email ?>" style="color:#bbbbbb;font-weight:bold;text-decoration:none;font-size:12px!important" target="_blank">ici</a></div>

                                        </td>
                                    </tr>
                                </tbody></table>
                            </td>
                        </tr>
        </tbody>
        </table>
        </div>
	</body>
</html>