# Create the temp directory that is the home of the uploaded images

iniFile="image-gallery-settings.ini"

tempDir=`grep "^temp_dir =" $iniFile | cut -d= -f2 | sed 's/"\(.*\)\/"/\1/'`

if [ ! -d $tempDir ]
then
    mkdir $tempDir
    chmod 777 $tempDir
    echo "Directory \""$tempDir"\" is Created!"
fi

echo "Installed successfully!"

exit