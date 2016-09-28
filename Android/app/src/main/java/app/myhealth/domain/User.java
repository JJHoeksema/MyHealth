package app.myhealth.domain;

import com.google.gson.annotations.SerializedName;

/**
 * Created by Werk on 27-9-2016.
 */
public class User extends AbstractEntity
{
    @SerializedName("User-email")
    private String _email;

    @SerializedName("User-id")
    private Integer _id;

    public void setEmail(String email)
    {
        this._email = email;
    }

    public String getEmail()
    {
        return _email;
    }

    @Override
    public Integer getId()
    {
        return _id;
    }

    @Override
    public void setId(Integer id)
    {
        this._id = id;
    }


}
