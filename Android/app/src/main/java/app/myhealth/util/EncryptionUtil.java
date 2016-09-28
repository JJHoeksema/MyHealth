package app.myhealth.util;

import android.provider.Settings;

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

}
