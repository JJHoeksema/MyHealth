package app.myhealth;

import android.content.Intent;
import android.graphics.Color;
import android.os.AsyncTask;
import android.support.v7.app.AppCompatActivity;

import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.AutoCompleteTextView;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;

import com.google.gson.Gson;

import app.myhealth.api.ApiCommunicator;
import app.myhealth.domain.Authenticate;
import app.myhealth.domain.User;
import app.myhealth.login.AsyncLogin;
import app.myhealth.menu.NavigationDrawerActivity;
import app.myhealth.util.EncryptionUtil;

/**
 * A login screen that offers login via email/password.
 */
public class LoginActivity extends AppCompatActivity {

    /**
     * Keep track of the login task to ensure we can cancel it if requested.
     */
    // UI references.
    private AutoCompleteTextView mEmailView;
    private EditText mPasswordView;
    private Button mSignInButton;
    private TextView mLoginFeedback;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);
        Intent intent = new Intent(this, NavigationDrawerActivity.class);

        // Set up the login form.
        mEmailView = (AutoCompleteTextView) findViewById(R.id.email);
        mPasswordView = (EditText) findViewById(R.id.password);

        mSignInButton = (Button) findViewById(R.id.email_sign_in_button);

        mLoginFeedback = (TextView) findViewById(R.id.login_feedback);

        mSignInButton.setOnClickListener(new View.OnClickListener()
        {
            @Override
            public void onClick(View v)
            {
                doLoginCall();
            }
        });
    }

    private void doLoginCall()
    {
        AsyncLogin login = new AsyncLogin();
        login.setLoginActivity(this);
        login.setAuthenticate( new Authenticate( mEmailView.getText().toString(), EncryptionUtil.getMD5(mPasswordView.getText().toString()) ));
        login.execute();
    }

    public void login(User user)
    {
        if( user != null )
        {
            Database.setUser(user);
        }
        else
        {
            mLoginFeedback.setText("Ongeldige login gegevens.");
            mLoginFeedback.setTextColor(Color.rgb(200,0,0));
        }

    }

    /**
     * Attempts to sign in or register the account specified by the login form.
     * If there are form errors (invalid email, missing fields, etc.), the
     * errors are presented and no actual login attempt is made.
     */
    private void attemptLogin() {

    }
    private boolean isEmailValid(String email) {
        //TODO: Replace this with your own logic
        return email.contains("@");
    }

    private boolean isPasswordValid(String password) {
        //TODO: Replace this with your own logic
        return password.length() > 4;
    }

}

