# Runs the Image Gallery deployment steps

# Prompt the user for the right path to infusion library
echo -n "Please enter the path to infusion (for example, ../infusion): "
read infusion_path

# Remove the ending slash in the given path if it has one
infusion_path=`echo "${infusion_path}" | sed -e "s/\/*$//" `

# Create the temp directory that is the home of the uploaded images
mkdir temp
chmod 777 temp

# Set the correct infusion path
sed "s#\[INFUSION_PATH\]#"$infusion_path"#g" uploader.html > uploader_new.html
mv uploader_new.html uploader.html
sed "s#\[INFUSION_PATH\]#"$infusion_path"#g" js/uploader.js > js/uploader_new.js
mv js/uploader_new.js js/uploader.js

exit