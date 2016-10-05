package app.myhealth;

import com.google.gson.Gson;

import org.junit.Test;

import app.myhealth.domain.Authenticate;
import app.myhealth.domain.User;
import app.myhealth.util.EncryptionUtil;

import static org.junit.Assert.assertEquals;

/**
 * Created by Werk on 28-9-2016.
 */
public class ApiTest
{
    private String authenticateJsonString = "{\"User-email\":\"thoeksema00@gmail.com\",\"User-password\":\"" + EncryptionUtil.getMD5("h") + "\"}";
    private Gson _gson = new Gson();

    @Test
    public void authenticate_gson_string()
    {
        Authenticate user = new Authenticate("thoeksema00@gmail.com", EncryptionUtil.getMD5("h"));

        assertEquals( _gson.toJson(user), authenticateJsonString );
    }

    @Test
    public void passwordTest()
    {
        assertEquals("2510c39011c5be704182423e3a695e91", EncryptionUtil.getMD5("h"));
    }

    @Test
    public void authenticate()
    {
        String result =
                new ApiConnection().connectWithResponse(
                        "login/"+EncryptionUtil.getApiToken(""),
                        authenticateJsonString
                );

        User user = _gson.fromJson(result, User.class);

        assertEquals(new Integer(79), user.getId());
        assertEquals("achternaam", user.getAchternaam());
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
