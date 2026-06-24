const { Jimp } = require('jimp');
const path = 'public/images/docteur-dressing-logo.png';
const out = 'public/images/docteur-dressing-logo-square.png';

Jimp.read(path)
  .then(img => {
    const size = Math.max(img.bitmap.width, img.bitmap.height, 256);
    return new Jimp(size, size, 0x00000000, (err, bg) => {
      if (err) throw err;
      const x = (size - img.bitmap.width) / 2;
      const y = (size - img.bitmap.height) / 2;
      bg.composite(img, x, y);
      return bg.writeAsync(out);
    });
  })
  .then(() => console.log('square created'))
  .catch(err => { console.error(err); process.exit(1); });
