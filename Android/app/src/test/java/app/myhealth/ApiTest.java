package app.myhealth;

import android.util.Log;

import com.google.gson.Gson;

import org.junit.Test;
import org.junit.runner.RunWith;
import org.mindrot.jbcrypt.BCrypt;

import java.util.concurrent.ExecutionException;

import app.myhealth.api.ApiCommunicator;
import app.myhealth.api.Connector;
import app.myhealth.domain.Authenticate;
import app.myhealth.domain.User;
import app.myhealth.util.EncryptionUtil;

import static org.junit.Assert.assertEquals;

/**
 * Created by Werk on 28-9-2016.
 */
public class ApiTest
{
    private String authenticateJsonString = "{\"User-email\":\"thoeksem00@gmail.com\",\"User-password\":\"h\"}";

    @Test
    public void authenticate_gson_string()
    {

        Gson gson = new Gson();
        Authenticate user = new Authenticate("thoeksem00@gmail.com", "h");

        assertEquals( gson.toJson(user), authenticateJsonString );
    }

    @Test
    public void authenticate()
    {
        String result = new Connector().doInBackground("login", authenticateJsonString);

        assertEquals("", result);

    }

    @Test
    public void password_encryption() throws Exception {
        String password = "h";
        String hashed = "$2y$12$mMCsZWL9kT/yN/LMIJ78uucb64a3SeBnI0pip9gPf3pqrDue5N/he";
        assertEquals(
                EncryptionUtil.getBlowfish(password),
                hashed
        );
    }
}
