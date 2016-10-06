package app.myhealth.measurement;

import android.os.AsyncTask;
import android.util.Log;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;

import java.util.Arrays;
import java.util.List;

import app.myhealth.api.ApiConnection;
import app.myhealth.domain.Readings;
import app.myhealth.fragments.MeasurementFragment;
import app.myhealth.util.EncryptionUtil;

/**
 * Created by Werk on 5-10-2016.
 */
public class AsyncMeasurements extends AsyncTask<String, String, List<Readings>>
{
    Gson _gson;
    MeasurementFragment _measurementFragment;

    @Override
    protected void onPreExecute()
    {
        _gson = new GsonBuilder()
        .setDateFormat("yyyy-MM-dd HH:mm:ss")
        .create();
    }

    @Override
    protected List<Readings> doInBackground(String... params)
    {
        String result =
                new ApiConnection().connectWithResponse(
                        ("measurements/"+ EncryptionUtil.getApiToken("")+"/"+80),
                        ""
                );

        Log.d("result in asynctask", result);

        Readings[] measurements =  _gson.fromJson(result, Readings[].class);

        return  Arrays.asList(measurements);
    }

    @Override
    protected void onPostExecute(List<Readings> measurements)
    {
        _measurementFragment.updateTableView(measurements);
    }

    public void setMeasurementFragment(MeasurementFragment measurementFragment)
    {
        _measurementFragment = measurementFragment;
    }

}
