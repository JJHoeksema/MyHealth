package app.myhealth.fragments;

import android.content.Context;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TableLayout;
import android.widget.TableRow;
import android.widget.TextView;

import java.text.SimpleDateFormat;
import java.util.List;

import app.myhealth.Database;
import app.myhealth.R;
import app.myhealth.domain.Readings;
import app.myhealth.measurement.AsyncMeasurements;
import app.myhealth.util.EncryptionUtil;

public class MeasurementFragment extends Fragment
{
    Context context;
    TableLayout tableLayout;

    public static MeasurementFragment newInstance(String param1, String param2) {
        return new MeasurementFragment();
    }
    public MeasurementFragment() {
        // Required empty public constructor
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        context = this.getContext();
    }
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState)
    {
        View view = inflater.inflate(R.layout.fragment_measurement, container, false);

        tableLayout = (TableLayout) view.findViewById(R.id.measurement_table);

        AsyncMeasurements asyncMeasurements = new AsyncMeasurements();
        asyncMeasurements.setMeasurementFragment(this);
        asyncMeasurements.execute();

        return view;
    }

    public void updateTableView(List<Readings> measurements)
    {
        Log.d("measurements size", ""+measurements.size());

        for (Readings m : measurements) {
            TableRow row = new TableRow(context);

            TextView typeView = new TextView(context);
            typeView.setText(m.getNaam());
            row.addView(typeView);

            TextView valueView = new TextView(context);
            valueView.setText(m.getValue());
            row.addView(valueView);

            TextView dateView = new TextView(context);
            dateView.setText( new SimpleDateFormat("dd-MM-yyyy HH:mm").format(m.getDatum()) );
            row.addView(dateView);

            tableLayout.addView(row);
        }

    }

}
