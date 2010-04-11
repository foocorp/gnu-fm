package fm.libre.droid;

import java.io.IOException;
import java.io.StringReader;

import javax.xml.parsers.ParserConfigurationException;
import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;

import org.xml.sax.Attributes;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.DefaultHandler;

import android.util.Log;

public class WSParser extends DefaultHandler {

	private String currentElement;
	private String key = "";
	
	public void startElement(String uri, String name, String qName, Attributes atts) {
		this.currentElement = name;
	}

	public void endElement(String uri, String name, String qName) throws SAXException {
		
	}
	
	public void characters(char ch[], int start, int length) {
		String chars = new String(ch).substring(start, start + length).trim();
		Log.d("libredroid", chars);
		if (this.currentElement.equals("key")) {
			this.key += chars;
		}
	}
	
	public void parse(String xspf) throws SAXException, ParserConfigurationException, IOException {
		SAXParserFactory spf = SAXParserFactory.newInstance();
		SAXParser sp = spf.newSAXParser();
		XMLReader xr = sp.getXMLReader();
		xr.setContentHandler(this);
		xr.parse(new InputSource(new StringReader(xspf)));
	}
	
	public String getKey() {
		return key;
	}
	
}
