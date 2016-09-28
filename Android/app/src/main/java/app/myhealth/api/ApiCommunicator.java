package app.myhealth.api;

import com.google.gson.Gson;

import java.security.NoSuchAlgorithmException;
import java.security.NoSuchProviderException;

import javax.crypto.BadPaddingException;
import javax.crypto.Cipher;
import javax.crypto.IllegalBlockSizeException;
import javax.crypto.NoSuchPaddingException;

import app.myhealth.domain.Authenticate;
import app.myhealth.domain.User;
import app.myhealth.util.EncryptionUtil;

/**
 * Created by Werk on 27-9-2016.
 */
public abstract class ApiCommunicator
{
    private static final Gson _gson = new Gson();

    public static User authenticateUser(String username, String password)
    {
        String passwordEncrypted = EncryptionUtil.getBlowfish(password);
        String jsonData = _gson.toJson( new Authenticate(username, passwordEncrypted) );

        return getObjectFromJson( ApiCall.getJsonUser(jsonData), User.class );
    }

    public static User getUser(int id)
    {
        return getObjectFromJson(ApiCall.getJsonUser(id), User.class);
    }

    public static <T> T getObjectFromJson(String jsonString, Class className)
    {
        return (T) _gson.fromJson(jsonString, className);
    }

}