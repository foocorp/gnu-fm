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

import java.io.IOException;
import java.io.StringReader;
import java.util.ArrayList;

import javax.xml.parsers.ParserConfigurationException;
import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;

import org.xml.sax.Attributes;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.DefaultHandler;

import android.util.Log;

public class Playlist extends DefaultHandler {
	private ArrayList<Song> playlist;
	private Song processingSong;
	private String currentElement;
	
	Playlist() {
		this.playlist = new ArrayList<Song>();
	}
	
	public int size() {
		return this.playlist.size();
	}

	public Song getSong(int song) {
		return this.playlist.get(song);
	}
	
	public void startElement(String uri, String name, String qName, Attributes atts) {
		this.currentElement = name;
		Log.d("libredroid", "Processing: " + name);
		if (name.equals("track")) {
			this.processingSong = new Song();
		}
	}

	public void endElement(String uri, String name, String qName) throws SAXException {
		if (name.equals("track")) {
			this.playlist.add(this.processingSong);
		}
	}
	
	public void characters(char ch[], int start, int length) {
		String chars = new String(ch).substring(start, start + length).trim();
		Log.d("libredroid", chars);
		if (this.processingSong == null) {
			return;
		}
		if (this.currentElement.equals("title")) {
			this.processingSong.title += chars;
		} else if (this.currentElement.equals("location")) {
			this.processingSong.location += chars;
		} else if (this.currentElement.equals("album")) {
			this.processingSong.album += chars;
		} else if (this.currentElement.equals("creator")) {
			this.processingSong.artist += chars;
		} else if (this.currentElement.equals("image")) {
			this.processingSong.imageURL += chars;
		} else if (this.currentElement.equals("artisturl")) {
			this.processingSong.artistURL += chars;
		} else if (this.currentElement.equals("albumurl")) {
			this.processingSong.albumURL += chars;
		} else if (this.currentElement.equals("trackurl")) {
			this.processingSong.trackURL += chars;
		} else if (this.currentElement.equals("downloadurl")) {
			this.processingSong.downloadURL += chars;
		}
	}
	
	public void parse(String xspf) throws SAXException, ParserConfigurationException, IOException {
		SAXParserFactory spf = SAXParserFactory.newInstance();
		SAXParser sp = spf.newSAXParser();
		XMLReader xr = sp.getXMLReader();
		xr.setContentHandler(this);
		xr.parse(new InputSource(new StringReader(xspf)));
	}
	
	public String toString() {
		return this.playlist.toString();
	}
}
