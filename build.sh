# Fix large build size for

./goodsign app:build --build-version=v1.05
cp ./builds/goodsign ../goodsign/public/downloads
ditto -c -k --sequesterRsrc  ../goodsign/public/downloads/goodsign ../goodsign/public/downloads/goodsign.zip
