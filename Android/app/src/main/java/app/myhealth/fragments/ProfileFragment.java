package app.myhealth.fragments;

import android.content.Context;
import android.net.Uri;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import app.myhealth.Database;
import app.myhealth.R;
import app.myhealth.domain.User;

public class ProfileFragment extends Fragment {

    private User user;

    public ProfileFragment() {
        // Required empty public constructor
    }

    public static ProfileFragment newInstance() {
        return new ProfileFragment();
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        user = Database.getUser();
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {

        View view = inflater.inflate(R.layout.fragment_profile, container, false);
        /*((TextView)view.findViewById(R.id.profile_naam)).append(user.);
        ((TextView)view.findViewById(R.id.profile_achternaam)).append(user.);
        ((TextView)view.findViewById(R.id.profile_land)).append(user.);
        ((TextView)view.findViewById(R.id.profile_plaats)).append(user.);
        ((TextView)view.findViewById(R.id.profile_straat)).append(user.);
        ((TextView)view.findViewById(R.id.profile_huisnr)).append(user.);
        ((TextView)view.findViewById(R.id.profile_toevoeging)).append(user.);
        ((TextView)view.findViewById(R.id.profile_telnr1)).append(user.);
        ((TextView)view.findViewById(R.id.profile_telnr2)).append(user.);
        ((TextView)view.findViewById(R.id.profile_email)).append(user.);
        ((TextView)view.findViewById(R.id.profile_reknr)).append(user.);*/

        return view;
    }
}
