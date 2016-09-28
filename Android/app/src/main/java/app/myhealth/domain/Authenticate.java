package app.myhealth.domain;

import com.google.gson.annotations.SerializedName;

/**
 * Created by Werk on 28-9-2016.
 */
public class Authenticate
{
    @SerializedName("User-email")
    private String _email;

    @SerializedName("User-password")
    private String _encryptedPassword;

    public Authenticate(String email, String encryptedWachtwoord)
    {
        this._email = email;
        this._encryptedPassword = encryptedWachtwoord;
    }

    public void setEmail(String email)
    {
        this._email = email;
    }

    public void setEncryptedPassword(String encryptedPassword)
    {
        this._encryptedPassword = encryptedPassword;
    }

    public String getEmail()
    {
        return _email;
    }

    public String getEncryptedPassword()
    {
        return _encryptedPassword;
    }

}
