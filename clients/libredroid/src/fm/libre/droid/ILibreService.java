package fm.libre.droid;

public interface ILibreService {
	
	public boolean login(String username, String password);
	public boolean isLoggedIn();
	public boolean isPlaying();
	public void setCurrentPage(int page);
	public int getCurrentPage();
	public void stop();
	public void play();
	public void next();
	public void prev();
	public void love();
	public void ban();
	public Song getSong();
	public Song getSong(int songNumber);
	public void tuneStation(String type, String station);
	public void togglePause();
	public String getStationName();

}
