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

public class Song {

	public String title;
	public String location;
	public String album;
	public String artist;
	public String imageURL;
	public String artistURL;
	public String albumURL;
	public String trackURL;
	public String downloadURL;
	
	Song() {
		this.title = "";
		this.location = "";
		this.album = "";
		this.artist = "";
		this.imageURL = "";
		this.artistURL = "";
		this.albumURL = "";
		this.trackURL = "";
		this.downloadURL = "";
	}
	
	public String toString() {
		return this.artist + " - " + this.title;
	}
	
}
