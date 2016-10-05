package app.myhealth.fragments;

import android.content.Context;
import android.net.Uri;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import android.widget.TableLayout;
import android.widget.TableRow;
import android.widget.TextView;

import app.myhealth.R;

public class MeasurementFragment extends Fragment {
    //TODO: private ArrayList<Measurement> measurements;

    public static MeasurementFragment newInstance(String param1, String param2) {
        return new MeasurementFragment();
    }
    public MeasurementFragment() {
        // Required empty public constructor
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_measurement, container, false);

        TableLayout table = (TableLayout)view.findViewById(R.id.measurement_table);

        Context context = this.getContext();
        for (Measurement m : measurements) {

            TableRow row = new TableRow(context);
            TextView typeView = new TextView(thi.)


            row.addView());
        }
        return view;
    }

}
