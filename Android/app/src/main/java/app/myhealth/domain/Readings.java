package app.myhealth.domain;

import com.google.gson.annotations.SerializedName;

/**
 * Created by Tom on 15-10-2016.
 */
public class Readings extends AbstractEntity
{
    @SerializedName("Readings-id")
    private Integer _id;

    @SerializedName("Readings-naam")
    private String _naam;

    @SerializedName("Readings-type")
    private int _type;

    @SerializedName("Readings-user_id")
    private int _user_id;

    @SerializedName("Readings-value")
    private String _value;

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


    public void setNaam(String naam)
    {
        this._naam = naam;
    }

    public String getNaam(){ return _naam; }


    public void setType(int type)
    {
        this._type = type;
    }

    public int getType(){ return _type; }


    public void setUser_id(int user_id)
    {
        this._user_id = user_id;
    }

    public int getUser_id(){ return _user_id; }


    public void setValue(String value)
    {
        this._value = value;
    }

    public String getValue(){ return _value; }


}
