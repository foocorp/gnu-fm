tsmarty2c.php ../templates/*.tpl > translatable_strings.c
xgettext translatable_strings.c
mv messages.po nixtape.pot
for langpo in `ls *.po`
do
	lang=`echo $langpo | cut -d '.' -f 1`
	echo "Updating $lang"
	msgmerge -U $langpo nixtape.pot
	mkdir -p $lang/LC_MESSAGES/
	msgfmt -o $lang/LC_MESSAGES/nixtape.mo $langpo 
done
