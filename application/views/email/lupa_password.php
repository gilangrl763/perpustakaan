<?
include_once("functions/string.func.php");
include_once("functions/date.func.php");

$this->load->model("UserLogin");
$this->load->model("Perusahaan");
$perusahaan = new Perusahaan();
$user_login = new UserLogin();

$reqId 		   = $reqParse1;
$reqPassword   = $reqParse2;

$user_login->selectByParamsMonitoring(array("A.USER_LOGIN_ID"=>$reqId));
$user_login->firstRow();


$perusahaan->selectByParams(array("A.PERUSAHAAN_ID"=>"1"));
$perusahaan->firstRow();
$PERUSAHAAN_NAMA        = $perusahaan->getField("NAMA");
$PERUSAHAAN_ALAMAT      = $perusahaan->getField("ALAMAT");
$PERUSAHAAN_TELEPON     = $perusahaan->getField("TELEPON");
$PERUSAHAAN_EMAIL       = $perusahaan->getField("EMAIL");
$PERUSAHAAN_WEBSITE     = $perusahaan->getField("WEBSITE");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Email Notification</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <style type="text/css">
        @media screen {
            @font-face {
                font-family: 'Lato';
                font-style: normal;
                font-weight: 400;
                src: local('Lato Regular'), local('Lato-Regular'), url(https://fonts.gstatic.com/s/lato/v11/qIIYRU-oROkIk8vfvxw6QvesZW2xOQ-xsNqO47m55DA.woff) format('woff');
            }

            @font-face {
                font-family: 'Lato';
                font-style: normal;
                font-weight: 700;
                src: local('Lato Bold'), local('Lato-Bold'), url(https://fonts.gstatic.com/s/lato/v11/qdgUG4U09HnJwhYI-uK18wLUuEpTyoUstqEm5AMlJo4.woff) format('woff');
            }

            @font-face {
                font-family: 'Lato';
                font-style: italic;
                font-weight: 400;
                src: local('Lato Italic'), local('Lato-Italic'), url(https://fonts.gstatic.com/s/lato/v11/RYyZNoeFgb0l7W3Vu1aSWOvvDin1pK8aKteLpeZ5c0A.woff) format('woff');
            }

            @font-face {
                font-family: 'Lato';
                font-style: italic;
                font-weight: 700;
                src: local('Lato Bold Italic'), local('Lato-BoldItalic'), url(https://fonts.gstatic.com/s/lato/v11/HkF_qI1x_noxlxhrhMQYELO3LdcAZYWl9Si6vvxL-qU.woff) format('woff');
            }
        }

        /* CLIENT-SPECIFIC STYLES */
        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        .bg-gradient {
            background: rgb(113 193 58);
            background: linear-gradient(90deg, rgb(113 193 58) 0%, rgb(0 163 219) 100%);
        }

        img {
            -ms-interpolation-mode: bicubic;
        }

        /* RESET STYLES */
        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        table {
            border-collapse: collapse !important;
        }

        body {
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        /* iOS BLUE LINKS */
        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        /* MOBILE STYLES */
        @media screen and (max-width:700px) {
            h1 {
                font-size: 32px !important;
                line-height: 32px !important;
            }
        }

        /* ANDROID CENTER FIX */
        div[style*="margin: 16px 0;"] {
            margin: 0 !important;
        }
    </style>
</head>

<body style="background-color: #f4f4f4; margin: 0 !important; padding: 0 !important;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <!-- LOGO -->
        <tr>
            <td bgcolor="#17c2a0" align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 700px;">
                    <tr>
                        <td align="center" valign="top" style="padding: 40px 10px 40px 10px;"> </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#17c2a0" align="center" style="padding: 0px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 700px;">
                    <tr>
                        <td bgcolor="#ffffff" align="left" valign="top" style="padding: 40px 20px 20px 30px; border-radius: 0px 0px 0px 0px; color: #111111; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 4px; line-height: 48px;">
                            <!-- <img src="<?=base_url()?>uploads/<?=$PERUSAHAAN_LOGO?>" width="200px" height="*" style="display: block; border: 0px;" /> -->
                            <img src="https://static.wikia.nocookie.net/logopedia/images/9/98/Trans_Marga_Jateng.png/revision/latest?cb=20191003012927" width="200px" height="*" style="display: block; border: 0px;" />
                        </td>
                        <td bgcolor="#ffffff" align="right" valign="top" style="padding: 40px 30px 20px 20px; border-radius: 0px 0px 0px 0px; color: #111111; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; letter-spacing: 2px; line-height: 48px;">
                            <p style="margin: 0;"><a href="<?=$PERUSAHAAN_WEBSITE?>" target="_blank" style="color: #17c2a0;"><?=$PERUSAHAAN_WEBSITE?></a></p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" bgcolor="#ffffff" align="center" valign="top" style="padding: 0px 30px 20px 30px; border-radius: 0px 0px 0px 0px; color: #111111; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; letter-spacing: 2px; line-height: 48px;">
                            <h2 style="text-align:center;font-weight:normal;font-family:'Lato', Helvetica, Arial, sans-serif;font-size:22px;margin-bottom:15px;color:#205478;line-height:135%; border-top:1px solid #9d9fa1; border-bottom:1px solid #9d9fa1; padding-top:10px;padding-bottom:7px;">Aplikasi Manajemen Kearsipan</h2>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 700px;">
                    <tr>
                        <td bgcolor="#ffffff" align="left" style="padding: 10px 30px 20px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 25px;">
                            <p style="margin: 0;">
                                Kepada Yth.
                                <br><b>Bapak/Ibu <?=$user_login->getField("NAMA_PEGAWAI")?></b>
                                <br>di
                                <br>&nbsp;&nbsp;-tempat
                            </p>
                            <p style="margin: 30px 0px 0px;">
                                Dengan hormat,
                                <br>Bersama dengan ini, kami sampaikan terdadapt Reset Password pada sistem <i>Aplikasi Manajemen Kearsipan</i>. Berikut USER ID terbaru Anda :
                            </p>
                            <p style="margin: 10px 0px 0px;">
                                <table style="text-align:left;font-family:'Lato', Helvetica, Arial, sans-serif;font-size:14px;margin-bottom:0;color:#666666;">
	                              	<tr>
	                                	<td>Nama</td>
	                                	<td>:</td>
	                                	<td><?=$user_login->getField("NAMA_PEGAWAI")?></td>
	                                </tr>
	                              	<tr>
	                                	<td>User Login</td>
	                                	<td>:</td>
	                                	<td><?=$user_login->getField("USER_LOGIN")?></td>
	                                </tr>
	                                <tr>
	                                	<td>Password Baru</td>
	                                	<td>:</td>
	                                	<td><?=$reqPassword?></td>
	                                </tr>
	                            </table>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#ffffff" align="left">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td bgcolor="#ffffff" align="center" style="padding: 0px 30px 10px 30px;">
                                        <table border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td bgcolor="#0158b1" align="center" style="border-radius: 3px;"><a href="<?=base_url()?>" target="_blank" style="font-size: 18px; font-family: 'Lato', Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; padding: 15px 25px; border-radius: 2px; display: inline-block;letter-spacing: 3px;">MENUJU APLIKASI</a></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr> <!-- COPY -->
                    <tr>
                        <td bgcolor="#ffffff" align="left" style="padding: 20px 30px 40px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 25px;">
                            <p style="margin: 0;">
                                Harap menjaga kerahasiaan dan keamanan USER ID Anda dengan baik. Silahkan login ke sistem menggunakan USER ID tersebut, serta silahkan segera perbarui Password Anda sesuai dengan ketentuan berlaku untuk keamanan.
                            </p>
                            <p style="margin: 10px 0px 0px;">
                                Demikian disampaikan, atas perhatian dan kerjasamanya diucapkan terima kasih.
                            </p>
                            <p style="margin: 30px 0px 0px;">
                                <i><b>Salam hormat,</b></i>
                                <br>Administrator Aplikasi Manajemen Kearsipan
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#f4f4f4" align="center" style="padding: 15px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 700px;">
                    <tr>
                        <td bgcolor="#0158b1" align="left" style="width:70%; padding: 20px 30px 20px 30px; border-radius: 4px 10px 10px 4px; color: #f3f3f3; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 11.5px; font-weight: 400; line-height: 18px;">
                            <p style="margin: 0;">
                                <span style="font-size: 13.5px;font-weight: 700;"><?=$PERUSAHAAN_NAMA?></span>
                                <br><?=$PERUSAHAAN_ALAMAT?>
                                <br>Telp. <?=$PERUSAHAAN_TELEPON?>
                            </p>
                        </td>
                        <td bgcolor="#17c2a0" align="left" style="width:30%; padding: 20px 30px 20px 30px; border-radius: 10px 4px 4px 10px; color: #f3f3f3; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 11.5px; font-weight: 400; line-height: 18px;">
                            <p style="margin: 0;">
                                <i>Info lebih lanjut :</i>
                                <br><a href="<?=$PERUSAHAAN_EMAIL?>" target="_blank" style="color: #f3f3f3;text-decoration:none;"><?=$PERUSAHAAN_EMAIL?></a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 700px;">
                    <tr>
                        <td bgcolor="#f4f4f4" align="left" style="padding: 0px 30px 30px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 12px; font-weight: 400; line-height: 18px;"> <br>
                            <p style="margin: 0;">&#169; <?=date("Y")?>. All rights reserved</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>