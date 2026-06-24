const { Jimp } = require('jimp');
console.log('keys', Object.getOwnPropertyNames(Jimp));
console.log('fromBitmap', Jimp.fromBitmap.toString());
console.log('fromBuffer', Jimp.fromBuffer.toString());
