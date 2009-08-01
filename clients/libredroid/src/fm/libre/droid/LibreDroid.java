/************************************************************************\
*                                                                       *
* Libre Droid - A libre.fm streaming music client for the Android       *
* mobile platform                                                       *
* Copyright (C) 2009  Michael Sheldon <mike@mikeasoft.com>              *
*                                                                       *
* This program is free software: you can redistribute it and/or modify  *
* it under the terms of the GNU General Public License as published by  *
* the Free Software Foundation, either version 3 of the License, or     *
* (at your option) any later version.                                   *
*                                                                       *
* This program is distributed in the hope that it will be useful,       *
* but WITHOUT ANY WARRANTY; without even the implied warranty of        *
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         *
* GNU General Public License for more details.                          *
*                                                                       *
* You should have received a copy of the GNU General Public License     *
* along with this program.  If not, see <http://www.gnu.org/licenses/>. *
*                                                                       *
*************************************************************************/

package fm.libre.droid;

import java.io.BufferedInputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.math.BigInteger;
import java.net.URI;
import java.net.URISyntaxException;
import java.net.URL;
import java.net.URLConnection;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;

import org.apache.http.HttpResponse;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;

import android.app.Activity;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.media.MediaPlayer;
import android.media.MediaPlayer.OnBufferingUpdateListener;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;
import android.widget.ViewAnimator;

public class LibreDroid extends Activity implements OnBufferingUpdateListener {
	private Playlist playlist;
	private String sessionKey;
	private int currentSong;
	private MediaPlayer mp;
	
    /** Called when the activity is first created. */
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.main);
        final Button loginButton = (Button) findViewById(R.id.loginButton);
        loginButton.setOnClickListener(new OnClickListener() {
        	public void onClick(View v) {
        		LibreDroid.this.login();
        	}
        });
        this.mp = new MediaPlayer();
        this.currentSong = 0;
        this.playlist = new Playlist();
        final Button folkButton = (Button) findViewById(R.id.folkButton);
        folkButton.setOnClickListener(new OnClickListener() {
            public void onClick(View v) {
                LibreDroid.this.tuneStation("globaltags", "folk");
            }
        });
    }
    
    public String httpGet(String url) throws URISyntaxException, ClientProtocolException, IOException {
    	DefaultHttpClient client = new DefaultHttpClient();
    	URI uri = new URI(url);
		HttpGet method = new HttpGet(uri);
		HttpResponse res = client.execute(method);
		ByteArrayOutputStream outstream = new ByteArrayOutputStream();  
		res.getEntity().writeTo(outstream);
		return outstream.toString();
    }
    
    public void tuneStation(String type, String station) {
    	try {
    		String output = this.httpGet("http://alpha.libre.fm/radio/adjust.php?session=" + this.sessionKey + "&url=librefm://" + type + "/" + station);
    		if (output.split(" ")[0].equals("FAILED")) {
    			Toast.makeText(this, output.substring(7), Toast.LENGTH_LONG).show();
    		} else {
    			final ViewAnimator view = (ViewAnimator) findViewById(R.id.viewAnimator);
    			String[] result = output.split("[=\n]");
    		    for (int x=0; x<result.length; x++)  {
    		    	if (result[x].trim().equals("stationname")) {
    		    		final TextView stationNameText = (TextView) findViewById(R.id.stationNameText);
    		    		stationNameText.setText(result[x+1].trim());
    		    	}
    		    }
    			view.showNext();
    			this.play();
    		}
    	} catch (Exception ex) {
    		Toast.makeText(this, "Unable to tune station: " + ex.getMessage(), Toast.LENGTH_LONG).show();
    	}
    }
    
    public void play() {
    	if (this.currentSong >= this.playlist.size()) {
    		this.getPlaylist();
    	}
    	Song song = this.playlist.getSong(currentSong);
    	Log.d("libredroid", "Song: " + this.playlist);
    	final TextView titleText = (TextView) findViewById(R.id.titleText);
    	final TextView artistText = (TextView) findViewById(R.id.artistText);
    	final ImageView albumImage = (ImageView) findViewById(R.id.albumImage); 
    	titleText.setText(song.title);
    	artistText.setText(song.artist);
    	if (song.imageURL.length() > 0) {
    		albumImage.setImageBitmap(this.getImageBitmap(song.imageURL));
    	} else {
    		albumImage.setImageResource(R.drawable.album);
    	}
    	try {
    		// Hack to get Jamendo MP3 stream instead of OGG because MediaPlayer
    		// doesn't support streaming OGG at the moment
    		this.mp.setDataSource(song.location.replace("ogg2", "mp31"));
    		Toast.makeText(this, "Buffering...", Toast.LENGTH_SHORT).show();
    		this.mp.setOnBufferingUpdateListener(this);
    		this.mp.prepareAsync();
    	} catch (Exception ex) {
    		Log.d("libredroid", "Couldn't play " + song.title + ": " + ex.getMessage());
    		this.next();
    	}
    	
    }
    
    private Bitmap getImageBitmap(String url) {
        Bitmap bm = null;
        try {
            URL aURL = new URL(url);
            URLConnection conn = aURL.openConnection();
            conn.connect();
            InputStream is = conn.getInputStream();
            BufferedInputStream bis = new BufferedInputStream(is);
            bm = BitmapFactory.decodeStream(bis);
            bis.close();
            is.close();
       } catch (IOException e) {
           
       }
       return bm;
    }
    
    public void next() {
    	this.currentSong++;
    	this.play();
    }
    
    public void prev() {
    	this.currentSong--;
    	this.play();
    }
    
    public void getPlaylist() {
    	try {
    		String xspf = this.httpGet("http://alpha.libre.fm/radio/xspf.php?sk=" + this.sessionKey + "&desktop=1.0");
    		this.playlist.parse(xspf);
    	} catch (Exception ex) {
    		Log.w("libredroid", "Unable to process playlist: " + ex.getMessage());
    		Toast.makeText(this, "Unable to process playlist: " + ex.getMessage(), Toast.LENGTH_LONG).show();
    	}
    }
    
    public void login() {
    	final EditText usernameEntry = (EditText) findViewById(R.id.usernameEntry);
    	final EditText passwordEntry = (EditText) findViewById(R.id.passwordEntry);
    	final ViewAnimator view = (ViewAnimator) findViewById(R.id.viewAnimator);
    	String username = usernameEntry.getText().toString();
    	String password = passwordEntry.getText().toString();
    	password = "blah243";
    	String passMD5 = "";
    	try {
    		MessageDigest md = MessageDigest.getInstance("MD5");
    		md.update(password.getBytes(), 0, password.length());
    		passMD5 = new BigInteger(1, md.digest()).toString(16);
    		if (passMD5.length() == 31) {
    			passMD5 = "0" + passMD5; 
    		}
    	} catch (NoSuchAlgorithmException ex) {
    		Toast.makeText(this, "MD5 hashing unavailable, unable to login.", Toast.LENGTH_LONG);
    	}
    	
    	try {
    		String output = this.httpGet("http://alpha.libre.fm/radio/handshake.php?username=" + username + "&passwordmd5=" + passMD5);
    		if (output.trim().equals("BADAUTH")) {
    			Toast.makeText(this, "Incorrect username or password", Toast.LENGTH_SHORT).show();
    		} else {
    		    String[] result = output.split("[=\n]");
    		    for (int x=0; x<result.length; x++)  {
    		    	if (result[x].trim().equals("session")) {
    		    		this.sessionKey = result[x+1].trim();
    		    	}
    		    }
    			view.showNext();
    		}
    	} catch (Exception ex) {
    		Toast.makeText(this, "Unable to connect to libre.fm server: " + ex.getMessage(), Toast.LENGTH_LONG).show();
    	}
    }

	public void onBufferingUpdate(MediaPlayer mp, int percent) {
		if (percent > 2 && !mp.isPlaying()) {
			this.mp.start();
		}
	}

}