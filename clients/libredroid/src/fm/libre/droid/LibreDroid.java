/************************************************************************\
 *                                                                       *
 * Libre Droid - A GNU FM streaming music client for the Android mobile  *
 * platform                                                              *
 * Copyright (C) 2009 Free Software Foundation, Inc                      *
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
import java.io.BufferedOutputStream;
import java.io.BufferedReader;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URI;
import java.net.URISyntaxException;
import java.net.URL;
import java.net.URLConnection;
import java.util.ArrayList;
import java.util.Arrays;
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

import android.app.ListActivity;
import android.content.BroadcastReceiver;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.ServiceConnection;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.media.MediaScannerConnection;
import android.media.MediaScannerConnection.MediaScannerConnectionClient;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Environment;
import android.os.IBinder;
import android.util.Log;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.MenuItem.OnMenuItemClickListener;
import android.view.View.OnClickListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageButton;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;
import android.widget.ViewAnimator;

public class LibreDroid extends ListActivity {
	private LibreServiceConnection libreServiceConn;
	private String username;
	private String password;
	private ArrayList<String> stations;

	/** Called when the activity is first created. */
	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		libreServiceConn = new LibreServiceConnection();
		bindService(new Intent(this, LibreService.class), libreServiceConn,	Context.BIND_AUTO_CREATE);

		this.registerReceiver(new MediaButtonReceiver(), new IntentFilter(Intent.ACTION_MEDIA_BUTTON));
		this.registerReceiver(new UIUpdateReceiver(), new IntentFilter("LibreDroidNewSong"));
		setContentView(R.layout.main);
		
		// Load settings
		final SharedPreferences settings = getSharedPreferences("LibreDroid", MODE_PRIVATE);
		username = settings.getString("Username", "");
		password = settings.getString("Password", "");

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

		stations = new ArrayList<String>();
		try {
			BufferedReader stationReader = new BufferedReader(new InputStreamReader(openFileInput("libredroid-custom-stations.conf")));
			String station;
			while((station = stationReader.readLine()) != null) {
				stations.add(station);
			}
			stationReader.close();
		} catch (IOException ex) {
			Log.d("libredroid", ex.getMessage());
		}
		// Add default stations if empty
		if(stations.isEmpty()) {
			String radioButtons[] = { "Folk", "Rock", "Metal", "Classical", "Pop",
					"Punk", "Jazz", "Blues", "Rap", "Ambient", "Add A Custom Station..." };
			stations.addAll(Arrays.asList(radioButtons));
		}
		setListAdapter(new ArrayAdapter<String>(this, R.layout.list_item, stations));
		
		Button tagStation = (Button) findViewById(R.id.tagStationButton);
		tagStation.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				LibreDroid.this.nextPage();
			}
		});
		
		Button loveStation = (Button) findViewById(R.id.loveStationButton);
		loveStation.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				LibreDroid.this.libreServiceConn.service.tuneStation("user", username + "/loved");
				LibreDroid.this.nextPage();
				LibreDroid.this.nextPage();
				LibreDroid.this.nextPage();
			}
		});
		
		Button communityLoveStation = (Button) findViewById(R.id.communityStationButton);
		communityLoveStation.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				LibreDroid.this.libreServiceConn.service.tuneStation("community", "loved");
				LibreDroid.this.nextPage();
				LibreDroid.this.nextPage();
				LibreDroid.this.nextPage();
			}
		});

		final ImageButton nextButton = (ImageButton) findViewById(R.id.nextButton);
		nextButton.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				LibreDroid.this.libreServiceConn.service.next();
			}
		});
		final ImageButton prevButton = (ImageButton) findViewById(R.id.prevButton);
		prevButton.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				LibreDroid.this.libreServiceConn.service.prev();
			}
		});
		final ImageButton playPauseButton = (ImageButton) findViewById(R.id.playPauseButton);
		playPauseButton.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				LibreDroid.this.togglePause();
			}
		});
		final ImageButton saveButton = (ImageButton) findViewById(R.id.saveButton);
		saveButton.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				LibreDroid.this.save();
			}
		});
		final ImageButton loveButton = (ImageButton) findViewById(R.id.loveButton);
		loveButton.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				LibreDroid.this.libreServiceConn.service.love();
			}
		});
		final ImageButton banButton = (ImageButton) findViewById(R.id.banButton);
		banButton.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				LibreDroid.this.libreServiceConn.service.ban();
			}
		});
		final Button addStationButton = (Button) findViewById(R.id.addStationButton);
		addStationButton.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				LibreDroid.this.addStation();
			}
		});
	}

	@Override
	public void onDestroy() {
		super.onDestroy();
	}
	
	@Override
	public void onListItemClick(ListView l, View v, int pos, long id) {
		super.onListItemClick(l, v, pos, id);
		String selection = l.getItemAtPosition(pos).toString();
		if(selection.equals("Add A Custom Station...")) {			
			LibreDroid.this.nextPage();
		} else {
			LibreDroid.this.libreServiceConn.service.tuneStation("globaltags", selection);
			LibreDroid.this.nextPage();
			LibreDroid.this.nextPage();
		}
	}

	public void updateSong() {
		Song song = libreServiceConn.service.getSong();
		final TextView titleText = (TextView) findViewById(R.id.titleText);
		final TextView artistText = (TextView) findViewById(R.id.artistText);
		final TextView stationText = (TextView) findViewById(R.id.stationNameText);
		final ImageView albumImage = (ImageView) findViewById(R.id.albumImage);
		final ImageButton playPauseButton = (ImageButton) findViewById(R.id.playPauseButton);
		playPauseButton.setImageResource(R.drawable.pause);
		titleText.setText(song.title);
		artistText.setText(song.artist);
		stationText.setText(libreServiceConn.service.getStationName());
		if (song.imageURL.length() > 0) {
			new AlbumImageTask().execute(song.imageURL);
		} else {
			albumImage.setImageResource(R.drawable.album);
		}
	}
	
	public void addStation() {
		final EditText stationEntry = (EditText) findViewById(R.id.stationEntry);
		stations.add(0, stationEntry.getText().toString());
		try{
			FileOutputStream stationFile = openFileOutput("libredroid-custom-stations.conf", Context.MODE_PRIVATE);
			for (String station : stations) {
				stationFile.write(station.getBytes());
				stationFile.write('\n');
			}
			stationFile.close();
		} catch (IOException ex) {
			Log.w("libredroid", ex.getMessage());
		}
		setListAdapter(new ArrayAdapter<String>(this, R.layout.list_item, stations));
		this.prevPage();
	}

	@Override
	public boolean onKeyDown(int keyCode, KeyEvent event) {
		if ((keyCode == KeyEvent.KEYCODE_BACK)) {
			if (this.libreServiceConn.service.getCurrentPage() > 0) {
				if(this.libreServiceConn.service.getCurrentPage() == 4) {
					// Back three pages if we're on the player page to get back to the main menu
					this.prevPage();
					this.prevPage();
				}
				this.prevPage();
				LibreDroid.this.libreServiceConn.service.stop();
				return true;
			}
		}
		return super.onKeyDown(keyCode, event);
	}

	public String httpGet(String url) throws URISyntaxException,
			ClientProtocolException, IOException {
		DefaultHttpClient client = new DefaultHttpClient();
		URI uri = new URI(url);
		HttpGet method = new HttpGet(uri);
		HttpResponse res = client.execute(method);
		ByteArrayOutputStream outstream = new ByteArrayOutputStream();
		res.getEntity().writeTo(outstream);
		return outstream.toString();
	}

	public String httpPost(String url, String... params)
			throws URISyntaxException, ClientProtocolException, IOException {
		DefaultHttpClient client = new DefaultHttpClient();
		URI uri = new URI(url);
		HttpPost method = new HttpPost(uri);
		List<NameValuePair> paramPairs = new ArrayList<NameValuePair>(2);
		for (int i = 0; i < params.length; i += 2) {
			paramPairs.add(new BasicNameValuePair(params[i], params[i + 1]));
		}
		method.setEntity(new UrlEncodedFormEntity(paramPairs));
		method.getParams().setBooleanParameter(
				CoreProtocolPNames.USE_EXPECT_CONTINUE, false); // Disable
																// expect-continue,
																// caching
																// server
																// doesn't like
																// this
		HttpResponse res = client.execute(method);
		ByteArrayOutputStream outstream = new ByteArrayOutputStream();
		res.getEntity().writeTo(outstream);
		return outstream.toString();
	}

	public void togglePause() {
		final ImageButton playPauseButton = (ImageButton) findViewById(R.id.playPauseButton);
		if (libreServiceConn.service.isPlaying()) {
			playPauseButton.setImageResource(R.drawable.play);
		} else {
			playPauseButton.setImageResource(R.drawable.pause);
		}
		libreServiceConn.service.togglePause();
	}

	public void login() {
		final EditText usernameEntry = (EditText) findViewById(R.id.usernameEntry);
		final EditText passwordEntry = (EditText) findViewById(R.id.passwordEntry);
		username = usernameEntry.getText().toString();
		password = passwordEntry.getText().toString();
		boolean loggedIn = libreServiceConn.service.login(username, password);
		if (loggedIn) {
			nextPage();
		}
	}

	public void nextPage() {
		final ViewAnimator view = (ViewAnimator) findViewById(R.id.viewAnimator);
		view.showNext();
		libreServiceConn.service.setCurrentPage(view.getDisplayedChild());
	}

	public void prevPage() {
		final ViewAnimator view = (ViewAnimator) findViewById(R.id.viewAnimator);
		view.showPrevious();
		libreServiceConn.service.setCurrentPage(view.getDisplayedChild());
	}

	public void save() {
		Song song = this.libreServiceConn.service.getSong();
		Toast.makeText(LibreDroid.this,
				"Downloading \"" + song.title + "\" to your SD card.",
				Toast.LENGTH_LONG).show();
		new DownloadTrackTask().execute(song);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		MenuItem changeStation = menu.add(0, Menu.FIRST, 0, "Change Station")
				.setIcon(R.drawable.back);
		MenuItem quit = menu.add(0, 2, 0, "Quit").setIcon(R.drawable.quit);
		changeStation.setOnMenuItemClickListener(new OnMenuItemClickListener() {
			public boolean onMenuItemClick(MenuItem item) {
				final ViewAnimator view = (ViewAnimator) findViewById(R.id.viewAnimator);
				if (view.getDisplayedChild() == 4) {
					LibreDroid.this.libreServiceConn.service.stop();
					LibreDroid.this.prevPage();
					LibreDroid.this.prevPage();
					LibreDroid.this.prevPage();
					return true;
				} else {
					return false;
				}
			}
		});

		quit.setOnMenuItemClickListener(new OnMenuItemClickListener() {
			public boolean onMenuItemClick(MenuItem item) {
				LibreDroid.this.libreServiceConn.service.stop();
				LibreDroid.this.finish();
				return true;
			}
		});

		return super.onCreateOptionsMenu(menu);
	}

	private class AlbumImageTask extends AsyncTask<String, String, Bitmap> {

		protected Bitmap doInBackground(String... params) {
			String url = params[0];
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

		protected void onPostExecute(Bitmap bm) {
			final ImageView albumImage = (ImageView) findViewById(R.id.albumImage);
			albumImage.setImageBitmap(bm);
		}

	}

	private class DownloadTrackTask extends
			AsyncTask<Song, String, List<Object>> implements
			MediaScannerConnectionClient {

		private MediaScannerConnection msc;
		private String path;

		@Override
		protected List<Object> doInBackground(Song... params) {
			Song song = params[0];
			List<Object> res = new ArrayList<Object>();
			try {
				File root = Environment.getExternalStorageDirectory();
				if (!Environment.getExternalStorageState().equals(
						Environment.MEDIA_MOUNTED)) {
					res.add(false);
					res.add("Please ensure an SD card is inserted before attempting to download songs. " + Environment.getExternalStorageState());
					return res;
				}
				File musicDir = new File(root, "Music");
				if (!musicDir.exists()) {
					musicDir.mkdir();
				}

				File f = new File(musicDir, song.artist + " - " + song.title + ".ogg");
				this.path = f.getAbsolutePath();
				FileOutputStream fo = new FileOutputStream(f);
				URL aURL = new URL(song.location);
				HttpURLConnection conn = (HttpURLConnection) aURL.openConnection();
				conn.connect();
				if (conn.getResponseCode() == 301 || conn.getResponseCode() == 302 || conn.getResponseCode() == 307) {
					// Redirected
					aURL = new URL(conn.getHeaderField("Location"));
					conn = (HttpURLConnection) aURL.openConnection();
				}
				InputStream is = conn.getInputStream();
				BufferedInputStream bis = new BufferedInputStream(is);
				BufferedOutputStream bos = new BufferedOutputStream(fo);
				byte buf[] = new byte[1024];
				int count = 0;
				while ((count = bis.read(buf, 0, 1024)) != -1) {
					bos.write(buf, 0, count);
				}
				bos.close();
				fo.close();
				bis.close();
				is.close();
				res.add(true);
				res.add("Finished downloading \"" + song.title + "\"");
			} catch (Exception ex) {
				res.add(false);
				res.add("Unable to download \"" + song.title + "\": "
						+ ex.getMessage());
			}
			return res;
		}

		protected void onPostExecute(List<Object> result) {
			Boolean res = (Boolean) result.get(0);
			String msg = (String) result.get(1);
			if (res.booleanValue() == true) {
				// Update the media library so it knows about the new file
				this.msc = new MediaScannerConnection(LibreDroid.this, this);
				this.msc.connect();
			}
			Toast.makeText(LibreDroid.this, msg, Toast.LENGTH_LONG).show();
		}

		public void onMediaScannerConnected() {
			this.msc.scanFile(this.path, null);
		}

		public void onScanCompleted(String path, Uri uri) {

		}

	}

	private class UIUpdateReceiver extends BroadcastReceiver {

		@Override
		public void onReceive(Context context, Intent intent) {
			LibreDroid.this.runOnUiThread(new Runnable() {
				public void run() {
					LibreDroid.this.updateSong();
				}
			});
		}

	}

	private class MediaButtonReceiver extends BroadcastReceiver {

		@Override
		public void onReceive(Context context, Intent intent) {
			KeyEvent ev = (KeyEvent) intent.getExtras().get(
					Intent.EXTRA_KEY_EVENT);
			if (ev.getAction() == KeyEvent.ACTION_UP) {
				// Only perform the action on keydown/multiple
				return;
			}
			switch (ev.getKeyCode()) {
			case KeyEvent.KEYCODE_MEDIA_NEXT:
				LibreDroid.this.libreServiceConn.service.next();
				this.abortBroadcast();
				break;
			case KeyEvent.KEYCODE_MEDIA_PREVIOUS:
				LibreDroid.this.libreServiceConn.service.prev();
				this.abortBroadcast();
				break;
			case KeyEvent.KEYCODE_MEDIA_PLAY_PAUSE:
				LibreDroid.this.togglePause();
				this.abortBroadcast();
				break;
			}
		}

	}

	private class LibreServiceConnection implements ServiceConnection {

		public ILibreService service = null;

		public void onServiceConnected(ComponentName name, IBinder service) {
			this.service = (ILibreService) service;
			LibreDroid.this.runOnUiThread(new Runnable() {
				public void run() {
					final ViewAnimator view = (ViewAnimator) LibreDroid.this.findViewById(R.id.viewAnimator);
					view.setDisplayedChild(LibreServiceConnection.this.service.getCurrentPage());
					if (LibreServiceConnection.this.service.getCurrentPage() == 2) {
						LibreDroid.this.updateSong();
					}
				}
			});

		}

		public void onServiceDisconnected(ComponentName name) {

		}

	}

}