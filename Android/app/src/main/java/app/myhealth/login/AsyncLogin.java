package app.myhealth.login;

import android.os.AsyncTask;
import android.util.Log;

import com.google.gson.Gson;

import app.myhealth.api.ApiConnection;
import app.myhealth.api.Result;
import app.myhealth.domain.Authenticate;
import app.myhealth.domain.User;
import app.myhealth.util.EncryptionUtil;

/**
 * Created by Werk on 5-10-2016.
 */
public class AsyncLogin extends AsyncTask<String, String, User>
{
    Gson _gson = new Gson();
    LoginActivity _loginActivity;
    Authenticate _authAuthenticate;

    @Override
    protected User doInBackground(String... params)
    {
        String authenticateJsonString = _gson.toJson(_authAuthenticate);
        String result =
                new ApiConnection().connectWithResponse(
                        "login/"+ EncryptionUtil.getApiToken(""),
                        authenticateJsonString
                );

        Log.d("result", result);

        if( _gson.fromJson(result, Result.class).equals(Boolean.FALSE) )
        {
            return null;
        }

        return _gson.fromJson(result, User.class);
    }

    @Override
    protected void onPostExecute(User user)
    {
        if( user.getId() == null)
        {
            user = null;
        }

        _loginActivity.attemptLogin(user);
    }

    public void setLoginActivity(LoginActivity loginActivity)
    {
        _loginActivity = loginActivity;
    }

    public void setAuthenticate(Authenticate authenticate)
    {
        this._authAuthenticate = authenticate;
    }

}
