package app.myhealth;

import com.google.gson.Gson;

import org.junit.Test;

import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.TimeZone;

import app.myhealth.domain.Authenticate;
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
        String result = new ApiConnection().connectWithResponse("login/"+EncryptionUtil.getApiToken(authenticateJsonString), authenticateJsonString);

        assertEquals("", result);
    }

    @Test
    public void api_token()
    {
        assertEquals(EncryptionUtil.getApiToken(""), "48b76552a2058c48b74e2f62f12ee6d7");
    }

    @Test
    public void password_encryption() throws Exception {
        String password = "test";
        String hashed = "098f6bcd4621d373cade4e832627b4f6";
        assertEquals(
                EncryptionUtil.getMD5(password),
                hashed
        );
    }
}
