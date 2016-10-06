package app.myhealth.login;

import android.content.Intent;
import android.graphics.Color;
import android.support.v7.app.AppCompatActivity;

import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.AutoCompleteTextView;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;

import app.myhealth.Database;
import app.myhealth.R;
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
        Database.setUser(null);

        AsyncLogin login = new AsyncLogin();
        login.setLoginActivity(this);
        login.setAuthenticate( new Authenticate( mEmailView.getText().toString(), EncryptionUtil.getMD5(mPasswordView.getText().toString()) ));
        login.execute();
    }

    private void login(User user)
    {
        Database.setUser(user);
        Intent intent = new Intent(this, NavigationDrawerActivity.class);
        startActivity(intent);
    }

    public void attemptLogin(User user)
    {
        if( user != null )
        {
            login(user);
        }
        else
        {
            mLoginFeedback.setText("Ongeldige login gegevens.");
            mLoginFeedback.setTextColor(Color.rgb(200,0,0));
        }
    }

    private boolean isEmailValid(String email)
    {
        //TODO: Replace this with your own logic
        return email.contains("@");
    }

    private boolean isPasswordValid(String password)
    {
        //TODO: Replace this with your own logic
        return password.length() > 4;
    }

}