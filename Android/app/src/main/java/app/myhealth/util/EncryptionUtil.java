package app.myhealth.util;

import org.mindrot.jbcrypt.BCrypt;

/**
 * Created by Werk on 28-9-2016.
 */
public class EncryptionUtil
{

    public static String getBlowfish(String password)
    {
        return BCrypt.hashpw(password, BCrypt.gensalt(12) );
    }

    public static String getMD5(String txt) {
        try {
            java.security.MessageDigest md = java.security.MessageDigest.getInstance("MD5");
            byte[] array = md.digest(txt.getBytes());

            StringBuffer sb = new StringBuffer();
            for (int i = 0; i < array.length; ++i) {
                sb.append(Integer.toHexString((array[i] & 0xFF) | 0x100).substring(1,3));
            }

            return sb.toString();
        } catch (java.security.NoSuchAlgorithmException e) {
            //error action
        }
        return null;
    }

    public static String getApiToken(String content)
    {
        String secret = "kjashdfui";

        return getMD5( secret );
    }

}
