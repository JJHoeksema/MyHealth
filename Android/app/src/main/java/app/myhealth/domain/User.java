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

    @SerializedName("Naw-id")
    private Integer _nawId;

    @SerializedName("Naw-land")
    private String _land;

    @SerializedName("Naw-plaats")
    private String _plaats;

    @SerializedName("Naw-straat")
    private String _straat;

    @SerializedName("Naw-huisnummer")
    private Integer _huisnummer;

    @SerializedName("Naw-toevoeging")
    private String _toevoeging;

    @SerializedName("Naw-postcode")
    private String _postcode;

    @SerializedName("Naw-telnummer")
    private String _telefoonnummer;

    @SerializedName("Naw-reknummer")
    private String _rekeningnummer;

    @SerializedName("Naw-naam")
    private String _voornaam;

    @SerializedName("Naw-achternaam")
    private String _achternaam;

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

    public void setEmail(String email)
    {
        this._email = email;
    }

    public String getEmail()
    {
        return _email;
    }

    public Integer getNawId()
    {
        return _nawId;
    }

    public void setNawId(Integer _nawId)
    {
        this._nawId = _nawId;
    }

    public String getLand()
    {
        return _land;
    }

    public void setLand(String _land)
    {
        this._land = _land;
    }

    public String getPlaats()
    {
        return _plaats;
    }

    public void setPlaats(String _plaats)
    {
        this._plaats = _plaats;
    }

    public String getStraat()
    {
        return _straat;
    }

    public void setStraat(String _straat)
    {
        this._straat = _straat;
    }

    public Integer getHuisnummer()
    {
        return _huisnummer;
    }

    public void setHuisnummer(Integer _huisnummer)
    {
        this._huisnummer = _huisnummer;
    }

    public String getToevoeging()
    {
        return _toevoeging;
    }

    public void setToevoeging(String _toevoeging)
    {
        this._toevoeging = _toevoeging;
    }

    public String getPostcode()
    {
        return _postcode;
    }

    public void setPostcode(String _postcode)
    {
        this._postcode = _postcode;
    }

    public String getTelefoonnummer()
    {
        return _telefoonnummer;
    }

    public void setTelefoonnummer(String _telefoonnummer)
    {
        this._telefoonnummer = _telefoonnummer;
    }

    public String getRekeningnummer()
    {
        return _rekeningnummer;
    }

    public void setRekeningnummer(String _rekeningnummer)
    {
        this._rekeningnummer = _rekeningnummer;
    }

    public String getVoornaam()
    {
        return _voornaam;
    }

    public void setVoornaam(String _voornaam)
    {
        this._voornaam = _voornaam;
    }

    public String getAchternaam()
    {
        return _achternaam;
    }

    public void setAchternaam(String _achternaam)
    {
        this._achternaam = _achternaam;
    }
}
