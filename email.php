<?php
public function localAuthenticate($username,$credential) {

    $auth = Zend_Auth::getInstance();
    $auth->clearIdentity();

    $CI =& get_instance();

    /* USER AUTH  */

    // Define $username and $password
    // using ldap bind
    $ldaprdn  = 'ho' . "\\" .$username;     // ldap rdn or dn
    $ldappass = $credential;  // associated password

    $ldapconn = ldap_connect("172.16.30.106")
    or die("Could not connect to LDAP server.");

    ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

    // binding to ldap server
    $ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
    // verify binding

    if ($ldapbind)
    {}
    else
        return true;

    ?>
    <script language="javascript">
        alert('<?=$ldapbind?>');
        document.location.href = 'index';
    </script>
    <?


   $filter="(sAMAccountName=$username)";
   $result = ldap_search($ldapconn,"dc=ho,dc=pjbservices,dc=com",$filter);
   ldap_sort($ldapconn,$result,"sn");
   $info = ldap_get_entries($ldapconn, $result);

    $username = $info[0]["samaccountname"][0];

    if($username == ""){
        return false;
    }
    else
    {
        $this->getLoginInformation($username);
        return true;
    }
}