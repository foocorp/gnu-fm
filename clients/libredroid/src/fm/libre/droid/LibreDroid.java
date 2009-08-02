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
import java.util.ArrayList;
import java.util.Date;
import java.util.List;

import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;
import org.apache.http.params.CoreProtocolPNames;

import android.app.Activity;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.media.MediaPlayer;
import android.media.MediaPlayer.OnBufferingUpdateListener;
import android.media.MediaPlayer.OnCompletionListener;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageButton;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;
import android.widget.ViewAnimator;

public class LibreDroid extends Activity implements OnBufferingUpdateListener, OnCompletionListener {
	private Playlist playlist;
	private String sessionKey;
	private String scrobbleKey;
	private int currentSong;
	private MediaPlayer mp;
	private boolean playing;
	private boolean buffering;
	
    /** Called when the activity is first created. */
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.main);
        this.mp = new MediaPlayer();
        
        // Load settings
        final SharedPreferences settings = getSharedPreferences("LibreDroid", MODE_PRIVATE);
        String username = settings.getString("Username", "");
        String password = settings.getString("Password", "");
        
        final EditText usernameEntry = (EditText) findViewById(R.id.usernameEntry);
        final EditText passwordEntry = (EditText) findViewById(R.id.passwordEntry);
        usernameEntry.setText(username);
        passwordEntry.setText(password);
        
        final Button loginButton = (Button) findViewById(R.id.loginButton);
        loginButton.setOnClickListener(new OnClickListener() {
        	public void onClick(View v) {
        		Editor editor = settings.edit();
        		editor.putString("Username", usernameEntry.getText().toString());
        		editor.putString("Password", passwordEntry.getText().toString());
        		editor.commit();
        		LibreDroid.this.login();
        	}
        });
        this.currentSong = 0;
        this.playlist = new Playlist();
        
        // Setup buttons
        final Button folkButton = (Button) findViewById(R.id.folkButton);
        folkButton.setOnClickListener(new OnClickListener() {
            public void onClick(View v) {
                LibreDroid.this.tuneStation("globaltags", "folk");
            }
        });
        final ImageButton nextButton = (ImageButton) findViewById(R.id.nextButton);
        nextButton.setOnClickListener(new OnClickListener() {
        	public void onClick(View v) {
        		LibreDroid.this.next();
        	}
        });
        final ImageButton prevButton = (ImageButton) findViewById(R.id.prevButton);
        prevButton.setOnClickListener(new OnClickListener() {
        	public void onClick(View v) {
        		LibreDroid.this.prev();
        	}
        });
        final ImageButton playPauseButton = (ImageButton) findViewById(R.id.playPauseButton);
        playPauseButton.setOnClickListener(new OnClickListener() {
        	public void onClick(View v) {
        		LibreDroid.this.togglePause();
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
    
    public String httpPost(String url, String... params) throws URISyntaxException, ClientProtocolException, IOException {
    	DefaultHttpClient client = new DefaultHttpClient();
    	URI uri = new URI(url);
    	HttpPost method = new HttpPost(uri);
    	List<NameValuePair> paramPairs = new ArrayList<NameValuePair>(2);
    	for (int i = 0; i < params.length; i+=2) {
    		paramPairs.add(new BasicNameValuePair(params[i], params[i+1]));  
    	}  
    	method.setEntity(new UrlEncodedFormEntity(paramPairs));
    	method.getParams().setBooleanParameter(CoreProtocolPNames.USE_EXPECT_CONTINUE, false); // Disable expect-continue, caching server doesn't like this
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
    	this.playing = true;
    	this.buffering = true;
    	Song song = this.playlist.getSong(currentSong);
    	Log.d("libredroid", "Song: " + this.playlist);
    	final TextView titleText = (TextView) findViewById(R.id.titleText);
    	final TextView artistText = (TextView) findViewById(R.id.artistText);
    	final ImageView albumImage = (ImageView) findViewById(R.id.albumImage);
    	final ImageButton playPauseButton = (ImageButton) findViewById(R.id.playPauseButton);
    	playPauseButton.setImageResource(R.drawable.pause);
    	titleText.setText(song.title);
    	artistText.setText(song.artist);
    	if (song.imageURL.length() > 0) {
    		albumImage.setImageBitmap(this.getImageBitmap(song.imageURL));
    	} else {
    		albumImage.setImageResource(R.drawable.album);
    	}
    	try {
    		this.mp.reset();
    		// Hack to get Jamendo MP3 stream instead of OGG because MediaPlayer
    		// doesn't support streaming OGG at the moment
    		this.mp.setDataSource(song.location.replace("ogg2", "mp31"));
    		this.mp.setOnBufferingUpdateListener(this);
            this.mp.setOnCompletionListener(this);
    		this.mp.prepareAsync();
    		// Send now playing data
    		this.httpPost("http://turtle.libre.fm/nowplaying/1.2/", "s", this.scrobbleKey, "a", song.artist, "t", song.title);
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
    	mp.stop();
    	this.currentSong++;
    	this.play();
    }
    
    public void prev() {
    	if (this.currentSong > 0) {
    		mp.stop();
    		this.currentSong--;
    		this.play();
    	}
    }
    
    public void togglePause() {
    	final ImageButton playPauseButton = (ImageButton) findViewById(R.id.playPauseButton);
    	if (mp.isPlaying()) {
    		mp.pause();
    		playPauseButton.setImageResource(R.drawable.play);
    	} else {
    		mp.start();
    		playPauseButton.setImageResource(R.drawable.pause);
    	}
    	this.playing = !this.playing;
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
    	String passMD5 = "";
    	String token = "";
    	long timestamp = new Date().getTime() / 1000;
    	try {
    		MessageDigest md = MessageDigest.getInstance("MD5");
    		md.update(password.getBytes(), 0, password.length());
    		passMD5 = new BigInteger(1, md.digest()).toString(16);
    		if (passMD5.length() == 31) {
    			passMD5 = "0" + passMD5; 
    		}
    		token = passMD5 + Long.toString(timestamp);
    		md.update(token.getBytes(), 0, token.length());
    		token = new BigInteger(1, md.digest()).toString(16);
    		if (token.length() == 31) {
    			token = "0" + token;
    		}
    	} catch (NoSuchAlgorithmException ex) {
    		Toast.makeText(this, "MD5 hashing unavailable, unable to login.", Toast.LENGTH_LONG);
    	}
    	
    	try {
    		// Login for streaming
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
    		// Login for scrobbling
    		output = this.httpGet("http://turtle.libre.fm/?hs=true&p=1.2&u=" + username + "&t=" + Long.toString(timestamp) + "&a=" + token + "&c=ldr" );    		
    		if (output.split("\n")[0].equals("OK")) {
    			this.scrobbleKey = output.split("\n")[1].trim();
    		}    		
    	} catch (Exception ex) {
    		Toast.makeText(this, "Unable to connect to libre.fm server: " + ex.getMessage(), Toast.LENGTH_LONG).show();
    	}
    }

	public void onBufferingUpdate(MediaPlayer mp, int percent) {
		if (percent > 2 && !mp.isPlaying() && this.playing) {
			this.mp.start();
		}
		if (percent > 99) {
			this.buffering = false;
		}
	}

	public void onCompletion(MediaPlayer mp) {
		if(!this.buffering) { // We get spurious complete messages if we're still buffering
			// Scrobble
			Song song = this.playlist.getSong(this.currentSong);
			try { 
				String time = Long.toString(new Date().getTime() / 1000);
				String output = this.httpPost("http://turtle.libre.fm/submissions/1.2/", "s", this.scrobbleKey, "a[0]", song.artist, "t[0]", song.title, "b[0]", song.album, "i[0]", time);
			} catch (Exception ex) {
				Log.d("libredroid", "Couldn't scrobble: " + ex.getMessage());
			}
			
			this.next();			
		}
	}

}