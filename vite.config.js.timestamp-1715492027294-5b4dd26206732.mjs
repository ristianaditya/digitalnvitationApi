// vite.config.js
import { defineConfig } from "file:///C:/xampp/htdocs/api/node_modules/vite/dist/node/index.js";
import laravel from "file:///C:/xampp/htdocs/api/node_modules/laravel-vite-plugin/dist/index.js";
import wppconnect from "file:///C:/xampp/htdocs/api/node_modules/@wppconnect-team/wppconnect/dist/index.js";
import {
  GoogleGenerativeAI,
  HarmCategory,
  HarmBlockThreshold
} from "file:///C:/xampp/htdocs/api/node_modules/@google/generative-ai/dist/index.mjs";
var MODEL_NAME = "gemini-1.0-pro";
var API_KEY = "AIzaSyBIsUZg9WfxbjgsehLjYkiWZ_k_hrEKrqQ";
var parts = [
  { text: "input: harga undangan digital di invite+" },
  { text: "output: Paket Bronze - Rp 25,000/bulan, Paket Silver - Rp 40,000/bulan sedangkan Paket Gold - Rp 60,000/bulan" },
  { text: "input: bagaimana cara memesan undangan digital" },
  { text: 'output: ketik "info" untuk mendapatkan info lebih lengkapnya' },
  { text: "input: link alamat undangan digital" },
  { text: "output: anda dapat mengunjungi website berikut www.inviteplus.com" },
  { text: "input: apa nama website undangan digital" },
  { text: "output: inviteplus" },
  { text: "input: apakah ada video tutorial untuk menggunakan undangan digital di invite+" },
  { text: "output: anda dapat melihat video tutorial di channel youtube inviteplus atau dengan link berikut https://www.youtube.com/inviteplus" },
  { text: "input: metode pembayaran apa saja yang bisa digunakan untuk pembayaran inviteplus" },
  { text: "output: Mulai dari transfer bank, kartu kredit, sampai e-wallet favoritmu pada midtrans" },
  { text: "input: apakah layanan ini menyediakan fitur gratis / free" },
  { text: "output: layanan inviteplus menyediakan layanan versi gratis untuk pengguna" },
  { text: "input: apa saja yang didapat pada layanan versi gratis / free pada undangan inviteplus" },
  { text: "output: anda dapat melihat detail info fitur dan keunggulan setiap layanan pada website resmi kami di www.inviteplus.com" }
];
wppconnect.create().then((client) => start(client)).catch((error) => console.log(error));
function start(client) {
  client.onMessage(async (message) => {
    const getMessage = message.body;
    const command = getMessage.split(" ");
    try {
      if (command[0] === ">sticker") {
        await client.sendImageAsSticker(message.from, command[1]).then((result) => {
          console.log("Result: ", result);
        }).catch((erro) => {
          console.error("Error when sending: ", erro);
        });
      } else if (command[0] === "info") {
        const welcome = [
          "\u{1F389} Halo di Invite+! \u{1F308}",
          "Seneng banget kamu milih Invite+ buat undangan digital kamu! \u{1F680} Yuk, bareng-bareng kita bikin momen spesial jadi makin memorable. \u{1F31F}",
          "Dengan aplikasi Invite+, bikin undangan jadi gampang dan seru, loh! \u{1F388} Kalo ada pertanyaan ato butuh bantuan, langsung aja chat kita di sini ya. Kita siap nolongin kamu! \u{1F917}",
          "Makasih banyak udah percaya sama kita! Selamat merencanain acaramu, semoga undangan digital dari Invite+ bener-bener ngebuat momen istimewa jadi lebih istimewa lagi! \u{1F38A}\u2728"
        ];
        client.sendListMessage(message.from, {
          buttonText: "Pilih opsi pertanyaan disini",
          description: welcome.join("\n\n"),
          sections: [
            {
              title: "Informasi Undangan",
              rows: [
                {
                  rowId: "harga",
                  title: "Harga Paket Langganan",
                  description: "Berapa harga undangan digital yang ditawarkan dan apa saja yang termasuk dalam paketnya?"
                },
                {
                  rowId: "tema",
                  title: "Desain dan Tema",
                  description: "Apakah ada pilihan desain atau tema khusus untuk undangan digital?"
                },
                {
                  rowId: "rsvp",
                  title: "RSVP",
                  description: "Apakah undangan digital ini mendukung fitur RSVP (konfirmasi kehadiran)?"
                }
              ]
            },
            {
              title: "Cara Menggunakan Undangan",
              rows: [
                {
                  rowId: "transaksi",
                  title: "Cara Pembayaran Paket Langganan",
                  description: "Bagaimana cara melakukan pembayaran paket langganan ?"
                },
                {
                  rowId: "qr",
                  title: "Cara konfirmasi Kehadiran dengan QR",
                  description: "Bagaimana cara melakukan konfirmasi kehadiran melalui QR ?"
                },
                {
                  rowId: "tutorial",
                  title: "Tutorial Penggunaan Aplikasi",
                  description: "apakah ada tutorial video penggunaan aplikasi undangan digital ?"
                }
              ]
            }
          ]
        });
      } else if (message?.listResponse?.singleSelectReply?.selectedRowId === "harga") {
        const harga = [
          "\u{1F31F}*Harga Langganan di Invite+ \u{1F31F}* \n\n",
          "Yo, teman-teman Invite+! Ini nih info harga langganan buat bikin undangan digital kece bareng kita! \u{1F4B8}\u2728 \n\n",
          "1\uFE0F\u20E3 *Paket Bronze - Rp 25,000/bulan:*\n",
          "1. Desain undangan keren\n",
          "2. Manajemen RSVP yang mudah\n",
          "3. Distribusi lewat WhatsApp\n\n",
          "2\uFE0F\u20E3 *Paket Silver - Rp 40,000/bulan:*\n",
          "1. Semua fitur di Paket Bronze\n",
          "2. Kustomisasi tambahan (warna, font, dll.)\n",
          "3. Integrasi dengan platform lain\n\n",
          "3\uFE0F\u20E3 *Paket Gold - Rp 60,000/bulan:*\n",
          "1. Semua fitur di Paket Silver\n",
          "2. Keamanan dan privasi premium\n",
          "3. Support pelanggan 24/7\n",
          "\u{1F4A1} *Catatan Penting:*\n",
          "Semua paket bisa dicoba GRATIS selama 7 hari. Harga sudah termasuk pajak.\n\n",
          "*Mau tanya-tanya atau mau mulai langganan? Langsung aja chat kita di sini. Kita siap ngebantu kamu!*\u{1F680}\u{1F44B}"
        ];
        client.sendText(message.from, harga.join(""));
      } else if (message?.listResponse?.singleSelectReply?.selectedRowId === "tema") {
        const tema = [
          "\u{1F3A8} *Temakan Undangan di Invite+* \u{1F308}",
          "Eh, udah tau gak nih, di Invite+ kita punya teman-teman undangan yang keren abis buat acaramu? \u{1F31F}\u2728",
          "Langsung aja lihat thema undangan di website Invite+. Kita siap bantuin mewujudkan undangan impian kamu! \u{1F308}\u{1F48C}"
        ];
        client.sendText(message.from, tema.join("\n\n"));
      } else if (message?.listResponse?.singleSelectReply?.selectedRowId === "rsvp") {
        const rsvp = [
          "\u{1F4C6} *RSVP di Invite+* \u{1F389}\n\n",
          "Wah, undangan aja udah kece, tapi RSVP-nya di Invite+ lebih kece lagi! Yuk, kenalan sama fiturnya. \u{1F31F}\u2728\n\n",
          "*Cara Pake RSVP di Invite+:*\n",
          "1. Buka aplikasi Invite+\n",
          "2. Pilih undanganmu\n",
          "3. Aktifkan fitur RSVP\n",
          "4. Custom pertanyaan kalau perlu\n",
          "5. Tunggu konfirmasi dari tamu yang antusias!\n\n",
          "\u{1F929}*Mau tau lebih banyak atau langsung pake fitur ini? Chat kita di sini, kita siap bantu!* \u{1F680}\u{1F4F2}"
        ];
        client.sendText(message.from, rsvp.join(""));
      } else if (message?.listResponse?.singleSelectReply?.selectedRowId === "transaksi") {
        const transaksi = [
          "\u{1F4B8} *Informasi Transaksi di Invite+* \u{1F680}\n\n",
          "Eh, denger-denger kamu mau tau nih gimana ceritanya urusan transaksi di Invite+? Tenang, kita punya semua jawabannya! \u{1F4B0}\u2728\n\n",
          "1\uFE0F\u20E3 *Paket Langganan:*\n",
          "1. Ada beberapa paket langganan yang bisa kamu pilih\n",
          "2. Mulai dari Bronze, Silver, sampai Gold, sesuai kebutuhanmu\n\n",
          "2\uFE0F\u20E3 *Uji Coba Gratis:*\n",
          "1. Nggak usah khawatir, bisa dicoba gratis selama 7 hari!\n",
          "2. Biar kamu bisa tau fiturnya sebelum bener-bener langganan\n\n",
          "3\uFE0F\u20E3 *Metode Pembayaran:*\n",
          "1. Terima pembayaran menggunakan berbagai metode\n",
          "2. Mulai dari transfer bank, kartu kredit, sampai e-wallet favoritmu\n"
        ];
        client.sendText(message.from, transaksi.join(""));
      } else if (message?.listResponse?.singleSelectReply?.selectedRowId === "qr") {
        const qr = [
          "*\u{1F50D} QR Absen di Invite+ \u{1F310}*\n\n",
          "Nih, yang lagi nyari cara praktis buat absen tamu di acara, Invite+ punya solusinya! Dengan QR Absen, semuanya jadi lebih cepat dan efisien. \u{1F680}\u2728\n\n",
          "1\uFE0F\u20E3 *Bagaimana Cara Kerjanya?*\n",
          "1. Pilih fitur QR Absen di aplikasi Invite+\n",
          "2. Generate QR Code unik untuk acaramu\n",
          "3. Tamu cukup scan QR Code itu untuk absen\n\n",
          "2\uFE0F\u20E3 *Kenapa Harus Pake QR Absen?*\n",
          "1. Proses absen lebih cepat dan tanpa ribet\n",
          "2. Akurat, karena langsung tercatat di sistem\n\n",
          "3\uFE0F\u20E3 *Pantau Kehadiran Langsung:*\n",
          "1. Lihat live report kehadiran tamu\n",
          "2. Praktis buat ngatur logistik acaramu\n\n",
          "\u{1F933} *Cara Pake QR Absen:*\n",
          "1. Buka aplikasi Invite+\n",
          "2. Pilih fitur QR Absen\n",
          "3. Generate QR Code\n",
          "4. Tamu scan QR Code, langsung tercatat!\n"
        ];
        client.sendText(message.from, qr.join(""));
      } else {
        async function run() {
          const genAI = new GoogleGenerativeAI(API_KEY);
          const model = genAI.getGenerativeModel({ model: MODEL_NAME });
          const generationConfig = {
            temperature: 0.9,
            topK: 1,
            topP: 1,
            maxOutputTokens: 2048
          };
          const safetySettings = [
            {
              category: HarmCategory.HARM_CATEGORY_HARASSMENT,
              threshold: HarmBlockThreshold.BLOCK_MEDIUM_AND_ABOVE
            },
            {
              category: HarmCategory.HARM_CATEGORY_HATE_SPEECH,
              threshold: HarmBlockThreshold.BLOCK_MEDIUM_AND_ABOVE
            },
            {
              category: HarmCategory.HARM_CATEGORY_SEXUALLY_EXPLICIT,
              threshold: HarmBlockThreshold.BLOCK_MEDIUM_AND_ABOVE
            },
            {
              category: HarmCategory.HARM_CATEGORY_DANGEROUS_CONTENT,
              threshold: HarmBlockThreshold.BLOCK_MEDIUM_AND_ABOVE
            }
          ];
          parts.push({ text: "input: " + getMessage });
          const result = await model.generateContent({
            contents: [{ role: "user", parts }],
            generationConfig,
            safetySettings
          });
          const response = result.response;
          parts.push({ text: response.text() });
          client.sendText(message.from, response.text());
        }
        run();
      }
      const parameter1 = process.argv[2];
      console.log("Parameter 1:", parameter1);
    } catch (error) {
      console.log(error.message);
    }
  });
}
var vite_config_default = defineConfig({
  plugins: [
    laravel({
      input: ["resources/css/app.css", "resources/js/app.js"],
      refresh: true
    })
  ]
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJDOlxcXFx4YW1wcFxcXFxodGRvY3NcXFxcYXBpXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ZpbGVuYW1lID0gXCJDOlxcXFx4YW1wcFxcXFxodGRvY3NcXFxcYXBpXFxcXHZpdGUuY29uZmlnLmpzXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ltcG9ydF9tZXRhX3VybCA9IFwiZmlsZTovLy9DOi94YW1wcC9odGRvY3MvYXBpL3ZpdGUuY29uZmlnLmpzXCI7aW1wb3J0IHsgZGVmaW5lQ29uZmlnIH0gZnJvbSAndml0ZSc7XG5pbXBvcnQgbGFyYXZlbCBmcm9tICdsYXJhdmVsLXZpdGUtcGx1Z2luJztcbmltcG9ydCAgd3BwY29ubmVjdCAgZnJvbSAnQHdwcGNvbm5lY3QtdGVhbS93cHBjb25uZWN0JztcbmltcG9ydCB7XG4gICAgR29vZ2xlR2VuZXJhdGl2ZUFJLFxuICAgIEhhcm1DYXRlZ29yeSxcbiAgICBIYXJtQmxvY2tUaHJlc2hvbGQsXG59IGZyb20gXCJAZ29vZ2xlL2dlbmVyYXRpdmUtYWlcIjtcbmNvbnN0IE1PREVMX05BTUUgPSBcImdlbWluaS0xLjAtcHJvXCI7XG5jb25zdCBBUElfS0VZID0gXCJBSXphU3lCSXNVWmc5V2Z4Ympnc2VoTGpZa2lXWl9rX2hyRUtycVFcIjtcblxuY29uc3QgcGFydHMgPSBbXG4gICAge3RleHQ6IFwiaW5wdXQ6IGhhcmdhIHVuZGFuZ2FuIGRpZ2l0YWwgZGkgaW52aXRlK1wifSxcbiAgICB7dGV4dDogXCJvdXRwdXQ6IFBha2V0IEJyb256ZSAtIFJwIDI1LDAwMC9idWxhbiwgUGFrZXQgU2lsdmVyIC0gUnAgNDAsMDAwL2J1bGFuIHNlZGFuZ2thbiBQYWtldCBHb2xkIC0gUnAgNjAsMDAwL2J1bGFuXCJ9LFxuICAgIHt0ZXh0OiBcImlucHV0OiBiYWdhaW1hbmEgY2FyYSBtZW1lc2FuIHVuZGFuZ2FuIGRpZ2l0YWxcIn0sXG4gICAge3RleHQ6IFwib3V0cHV0OiBrZXRpayBcXFwiaW5mb1xcXCIgdW50dWsgbWVuZGFwYXRrYW4gaW5mbyBsZWJpaCBsZW5na2FwbnlhXCJ9LFxuICAgIHt0ZXh0OiBcImlucHV0OiBsaW5rIGFsYW1hdCB1bmRhbmdhbiBkaWdpdGFsXCJ9LFxuICAgIHt0ZXh0OiBcIm91dHB1dDogYW5kYSBkYXBhdCBtZW5ndW5qdW5naSB3ZWJzaXRlIGJlcmlrdXQgd3d3Lmludml0ZXBsdXMuY29tXCJ9LFxuICAgIHt0ZXh0OiBcImlucHV0OiBhcGEgbmFtYSB3ZWJzaXRlIHVuZGFuZ2FuIGRpZ2l0YWxcIn0sXG4gICAge3RleHQ6IFwib3V0cHV0OiBpbnZpdGVwbHVzXCJ9LFxuICAgIHt0ZXh0OiBcImlucHV0OiBhcGFrYWggYWRhIHZpZGVvIHR1dG9yaWFsIHVudHVrIG1lbmdndW5ha2FuIHVuZGFuZ2FuIGRpZ2l0YWwgZGkgaW52aXRlK1wifSxcbiAgICB7dGV4dDogXCJvdXRwdXQ6IGFuZGEgZGFwYXQgbWVsaWhhdCB2aWRlbyB0dXRvcmlhbCBkaSBjaGFubmVsIHlvdXR1YmUgaW52aXRlcGx1cyBhdGF1IGRlbmdhbiBsaW5rIGJlcmlrdXQgaHR0cHM6Ly93d3cueW91dHViZS5jb20vaW52aXRlcGx1c1wifSxcbiAgICB7dGV4dDogXCJpbnB1dDogbWV0b2RlIHBlbWJheWFyYW4gYXBhIHNhamEgeWFuZyBiaXNhIGRpZ3VuYWthbiB1bnR1ayBwZW1iYXlhcmFuIGludml0ZXBsdXNcIn0sXG4gICAge3RleHQ6IFwib3V0cHV0OiBNdWxhaSBkYXJpIHRyYW5zZmVyIGJhbmssIGthcnR1IGtyZWRpdCwgc2FtcGFpIGUtd2FsbGV0IGZhdm9yaXRtdSBwYWRhIG1pZHRyYW5zXCJ9LFxuICAgIHt0ZXh0OiBcImlucHV0OiBhcGFrYWggbGF5YW5hbiBpbmkgbWVueWVkaWFrYW4gZml0dXIgZ3JhdGlzIC8gZnJlZVwifSxcbiAgICB7dGV4dDogXCJvdXRwdXQ6IGxheWFuYW4gaW52aXRlcGx1cyBtZW55ZWRpYWthbiBsYXlhbmFuIHZlcnNpIGdyYXRpcyB1bnR1ayBwZW5nZ3VuYVwifSxcbiAgICB7dGV4dDogXCJpbnB1dDogYXBhIHNhamEgeWFuZyBkaWRhcGF0IHBhZGEgbGF5YW5hbiB2ZXJzaSBncmF0aXMgLyBmcmVlIHBhZGEgdW5kYW5nYW4gaW52aXRlcGx1c1wifSxcbiAgICB7dGV4dDogXCJvdXRwdXQ6IGFuZGEgZGFwYXQgbWVsaWhhdCBkZXRhaWwgaW5mbyBmaXR1ciBkYW4ga2V1bmdndWxhbiBzZXRpYXAgbGF5YW5hbiBwYWRhIHdlYnNpdGUgcmVzbWkga2FtaSBkaSB3d3cuaW52aXRlcGx1cy5jb21cIn0sXG4gIF07XG5cbndwcGNvbm5lY3RcbiAgICAuY3JlYXRlKClcbiAgICAudGhlbigoY2xpZW50KSA9PiBzdGFydChjbGllbnQpKVxuICAgIC5jYXRjaCgoZXJyb3IpID0+IGNvbnNvbGUubG9nKGVycm9yKSk7XG5cbiAgICBmdW5jdGlvbiBzdGFydChjbGllbnQpIHtcbiAgICAgICAgY2xpZW50Lm9uTWVzc2FnZShhc3luYyAobWVzc2FnZSkgPT4ge1xuICAgICAgICAgICAgY29uc3QgZ2V0TWVzc2FnZSA9IG1lc3NhZ2UuYm9keTtcbiAgICAgICAgICAgIGNvbnN0IGNvbW1hbmQgPSBnZXRNZXNzYWdlLnNwbGl0KCcgJyk7XG4gICAgICAgICAgICB0cnkge1xuICAgICAgICAgICAgICAgIGlmIChjb21tYW5kWzBdID09PSAnPnN0aWNrZXInKSB7XG4gICAgICAgICAgICAgICAgICAgIGF3YWl0IGNsaWVudFxuICAgICAgICAgICAgICAgICAgICAgICAgLnNlbmRJbWFnZUFzU3RpY2tlcihtZXNzYWdlLmZyb20sIGNvbW1hbmRbMV0pXG4gICAgICAgICAgICAgICAgICAgICAgICAudGhlbigocmVzdWx0KSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ1Jlc3VsdDogJywgcmVzdWx0KTsgLy9yZXR1cm4gb2JqZWN0IHN1Y2Nlc3NcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAgICAgICAgICAgICAuY2F0Y2goKGVycm8pID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmVycm9yKCdFcnJvciB3aGVuIHNlbmRpbmc6ICcsIGVycm8pOyAvL3JldHVybiBvYmplY3QgZXJyb3JcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBlbHNlIGlmIChjb21tYW5kWzBdID09PSAnaW5mbycpIHtcbiAgICAgICAgICAgICAgICAgICAgY29uc3Qgd2VsY29tZSA9IFtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwiXHVEODNDXHVERjg5IEhhbG8gZGkgSW52aXRlKyEgXHVEODNDXHVERjA4XCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcIlNlbmVuZyBiYW5nZXQga2FtdSBtaWxpaCBJbnZpdGUrIGJ1YXQgdW5kYW5nYW4gZGlnaXRhbCBrYW11ISBcdUQ4M0RcdURFODAgWXVrLCBiYXJlbmctYmFyZW5nIGtpdGEgYmlraW4gbW9tZW4gc3Blc2lhbCBqYWRpIG1ha2luIG1lbW9yYWJsZS4gXHVEODNDXHVERjFGXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcIkRlbmdhbiBhcGxpa2FzaSBJbnZpdGUrLCBiaWtpbiB1bmRhbmdhbiBqYWRpIGdhbXBhbmcgZGFuIHNlcnUsIGxvaCEgXHVEODNDXHVERjg4IEthbG8gYWRhIHBlcnRhbnlhYW4gYXRvIGJ1dHVoIGJhbnR1YW4sIGxhbmdzdW5nIGFqYSBjaGF0IGtpdGEgZGkgc2luaSB5YS4gS2l0YSBzaWFwIG5vbG9uZ2luIGthbXUhIFx1RDgzRVx1REQxN1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJNYWthc2loIGJhbnlhayB1ZGFoIHBlcmNheWEgc2FtYSBraXRhISBTZWxhbWF0IG1lcmVuY2FuYWluIGFjYXJhbXUsIHNlbW9nYSB1bmRhbmdhbiBkaWdpdGFsIGRhcmkgSW52aXRlKyBiZW5lci1iZW5lciBuZ2VidWF0IG1vbWVuIGlzdGltZXdhIGphZGkgbGViaWggaXN0aW1ld2EgbGFnaSEgXHVEODNDXHVERjhBXHUyNzI4XCJcbiAgICAgICAgICAgICAgICAgICAgXTtcbiAgICAgICAgICAgICAgICAgICAgY2xpZW50LnNlbmRMaXN0TWVzc2FnZShtZXNzYWdlLmZyb20sIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGJ1dHRvblRleHQ6ICdQaWxpaCBvcHNpIHBlcnRhbnlhYW4gZGlzaW5pJyxcbiAgICAgICAgICAgICAgICAgICAgICAgIGRlc2NyaXB0aW9uOiB3ZWxjb21lLmpvaW4oJ1xcblxcbicpLFxuICAgICAgICAgICAgICAgICAgICAgICAgc2VjdGlvbnM6IFtcbiAgICAgICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aXRsZTogJ0luZm9ybWFzaSBVbmRhbmdhbicsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcm93czogW1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcm93SWQ6ICdoYXJnYScsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRpdGxlOiAnSGFyZ2EgUGFrZXQgTGFuZ2dhbmFuJyxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgZGVzY3JpcHRpb246ICdCZXJhcGEgaGFyZ2EgdW5kYW5nYW4gZGlnaXRhbCB5YW5nIGRpdGF3YXJrYW4gZGFuIGFwYSBzYWphIHlhbmcgdGVybWFzdWsgZGFsYW0gcGFrZXRueWE/JyxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcm93SWQ6ICd0ZW1hJyxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdGl0bGU6ICdEZXNhaW4gZGFuIFRlbWEnLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBkZXNjcmlwdGlvbjogJ0FwYWthaCBhZGEgcGlsaWhhbiBkZXNhaW4gYXRhdSB0ZW1hIGtodXN1cyB1bnR1ayB1bmRhbmdhbiBkaWdpdGFsPycsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJvd0lkOiAncnN2cCcsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRpdGxlOiAnUlNWUCcsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRlc2NyaXB0aW9uOiAnQXBha2FoIHVuZGFuZ2FuIGRpZ2l0YWwgaW5pIG1lbmR1a3VuZyBmaXR1ciBSU1ZQIChrb25maXJtYXNpIGtlaGFkaXJhbik/JyxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIF0sXG4gICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRpdGxlOiAnQ2FyYSBNZW5nZ3VuYWthbiBVbmRhbmdhbicsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcm93czogW1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICByb3dJZDogJ3RyYW5zYWtzaScsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aXRsZTogJ0NhcmEgUGVtYmF5YXJhbiBQYWtldCBMYW5nZ2FuYW4nLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgZGVzY3JpcHRpb246ICdCYWdhaW1hbmEgY2FyYSBtZWxha3VrYW4gcGVtYmF5YXJhbiBwYWtldCBsYW5nZ2FuYW4gPycsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJvd0lkOiAncXInLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdGl0bGU6ICdDYXJhIGtvbmZpcm1hc2kgS2VoYWRpcmFuIGRlbmdhbiBRUicsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBkZXNjcmlwdGlvbjogJ0JhZ2FpbWFuYSBjYXJhIG1lbGFrdWthbiBrb25maXJtYXNpIGtlaGFkaXJhbiBtZWxhbHVpIFFSID8nLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICByb3dJZDogJ3R1dG9yaWFsJyxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRpdGxlOiAnVHV0b3JpYWwgUGVuZ2d1bmFhbiBBcGxpa2FzaScsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBkZXNjcmlwdGlvbjogJ2FwYWthaCBhZGEgdHV0b3JpYWwgdmlkZW8gcGVuZ2d1bmFhbiBhcGxpa2FzaSB1bmRhbmdhbiBkaWdpdGFsID8nLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIF0sXG4gICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgXSxcbiAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGVsc2UgaWYgKG1lc3NhZ2U/Lmxpc3RSZXNwb25zZT8uc2luZ2xlU2VsZWN0UmVwbHk/LnNlbGVjdGVkUm93SWQgPT09ICdoYXJnYScpIHtcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgaGFyZ2EgPSBbXG4gICAgICAgICAgICAgICAgICAgICAgICBcIlx1RDgzQ1x1REYxRipIYXJnYSBMYW5nZ2FuYW4gZGkgSW52aXRlKyBcdUQ4M0NcdURGMUYqIFxcblxcblwiLFxuICAgICAgICAgICAgICAgICAgICAgICAgJ1lvLCB0ZW1hbi10ZW1hbiBJbnZpdGUrISBJbmkgbmloIGluZm8gaGFyZ2EgbGFuZ2dhbmFuIGJ1YXQgYmlraW4gdW5kYW5nYW4gZGlnaXRhbCBrZWNlIGJhcmVuZyBraXRhISBcdUQ4M0RcdURDQjhcdTI3MjggXFxuXFxuJyxcblxuICAgICAgICAgICAgICAgICAgICAgICAgJzFcdUZFMEZcdTIwRTMgKlBha2V0IEJyb256ZSAtIFJwIDI1LDAwMC9idWxhbjoqXFxuJyxcbiAgICAgICAgICAgICAgICAgICAgICAgICcxLiBEZXNhaW4gdW5kYW5nYW4ga2VyZW5cXG4nLFxuICAgICAgICAgICAgICAgICAgICAgICAgJzIuIE1hbmFqZW1lbiBSU1ZQIHlhbmcgbXVkYWhcXG4nLFxuICAgICAgICAgICAgICAgICAgICAgICAgJzMuIERpc3RyaWJ1c2kgbGV3YXQgV2hhdHNBcHBcXG5cXG4nLFxuXG4gICAgICAgICAgICAgICAgICAgICAgICAnMlx1RkUwRlx1MjBFMyAqUGFrZXQgU2lsdmVyIC0gUnAgNDAsMDAwL2J1bGFuOipcXG4nLFxuICAgICAgICAgICAgICAgICAgICAgICAgJzEuIFNlbXVhIGZpdHVyIGRpIFBha2V0IEJyb256ZVxcbicsXG4gICAgICAgICAgICAgICAgICAgICAgICAnMi4gS3VzdG9taXNhc2kgdGFtYmFoYW4gKHdhcm5hLCBmb250LCBkbGwuKVxcbicsXG4gICAgICAgICAgICAgICAgICAgICAgICAnMy4gSW50ZWdyYXNpIGRlbmdhbiBwbGF0Zm9ybSBsYWluXFxuXFxuJyxcblxuICAgICAgICAgICAgICAgICAgICAgICAgJzNcdUZFMEZcdTIwRTMgKlBha2V0IEdvbGQgLSBScCA2MCwwMDAvYnVsYW46KlxcbicsXG4gICAgICAgICAgICAgICAgICAgICAgICAnMS4gU2VtdWEgZml0dXIgZGkgUGFrZXQgU2lsdmVyXFxuJyxcbiAgICAgICAgICAgICAgICAgICAgICAgICcyLiBLZWFtYW5hbiBkYW4gcHJpdmFzaSBwcmVtaXVtXFxuJyxcbiAgICAgICAgICAgICAgICAgICAgICAgICczLiBTdXBwb3J0IHBlbGFuZ2dhbiAyNC83XFxuJyxcblxuICAgICAgICAgICAgICAgICAgICAgICAgJ1x1RDgzRFx1RENBMSAqQ2F0YXRhbiBQZW50aW5nOipcXG4nLFxuICAgICAgICAgICAgICAgICAgICAgICAgJ1NlbXVhIHBha2V0IGJpc2EgZGljb2JhIEdSQVRJUyBzZWxhbWEgNyBoYXJpLiBIYXJnYSBzdWRhaCB0ZXJtYXN1ayBwYWphay5cXG5cXG4nLFxuICAgICAgICAgICAgICAgICAgICAgICAgJypNYXUgdGFueWEtdGFueWEgYXRhdSBtYXUgbXVsYWkgbGFuZ2dhbmFuPyBMYW5nc3VuZyBhamEgY2hhdCBraXRhIGRpIHNpbmkuIEtpdGEgc2lhcCBuZ2ViYW50dSBrYW11ISpcdUQ4M0RcdURFODBcdUQ4M0RcdURDNEInLFxuICAgICAgICAgICAgICAgICAgICBdO1xuXG4gICAgICAgICAgICAgICAgICAgIGNsaWVudC5zZW5kVGV4dChtZXNzYWdlLmZyb20sIGhhcmdhLmpvaW4oJycpKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgZWxzZSBpZiAobWVzc2FnZT8ubGlzdFJlc3BvbnNlPy5zaW5nbGVTZWxlY3RSZXBseT8uc2VsZWN0ZWRSb3dJZCA9PT0gJ3RlbWEnKSB7XG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IHRlbWEgPSBbXG4gICAgICAgICAgICAgICAgICAgICAgICBcIlx1RDgzQ1x1REZBOCAqVGVtYWthbiBVbmRhbmdhbiBkaSBJbnZpdGUrKiBcdUQ4M0NcdURGMDhcIixcblxuICAgICAgICAgICAgICAgICAgICAgICAgJ0VoLCB1ZGFoIHRhdSBnYWsgbmloLCBkaSBJbnZpdGUrIGtpdGEgcHVueWEgdGVtYW4tdGVtYW4gdW5kYW5nYW4geWFuZyBrZXJlbiBhYmlzIGJ1YXQgYWNhcmFtdT8gXHVEODNDXHVERjFGXHUyNzI4JyxcblxuICAgICAgICAgICAgICAgICAgICAgICAgJ0xhbmdzdW5nIGFqYSBsaWhhdCB0aGVtYSB1bmRhbmdhbiBkaSB3ZWJzaXRlIEludml0ZSsuIEtpdGEgc2lhcCBiYW50dWluIG1ld3VqdWRrYW4gdW5kYW5nYW4gaW1waWFuIGthbXUhIFx1RDgzQ1x1REYwOFx1RDgzRFx1REM4QycsXG4gICAgICAgICAgICAgICAgICAgIF07XG5cbiAgICAgICAgICAgICAgICAgICAgY2xpZW50LnNlbmRUZXh0KG1lc3NhZ2UuZnJvbSwgdGVtYS5qb2luKCdcXG5cXG4nKSk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGVsc2UgaWYgKG1lc3NhZ2U/Lmxpc3RSZXNwb25zZT8uc2luZ2xlU2VsZWN0UmVwbHk/LnNlbGVjdGVkUm93SWQgPT09ICdyc3ZwJykge1xuICAgICAgICAgICAgICAgICAgICBjb25zdCByc3ZwID0gW1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJcdUQ4M0RcdURDQzYgKlJTVlAgZGkgSW52aXRlKyogXHVEODNDXHVERjg5XFxuXFxuXCIsXG5cbiAgICAgICAgICAgICAgICAgICAgICAgICdXYWgsIHVuZGFuZ2FuIGFqYSB1ZGFoIGtlY2UsIHRhcGkgUlNWUC1ueWEgZGkgSW52aXRlKyBsZWJpaCBrZWNlIGxhZ2khIFl1aywga2VuYWxhbiBzYW1hIGZpdHVybnlhLiBcdUQ4M0NcdURGMUZcdTI3MjhcXG5cXG4nLFxuXG4gICAgICAgICAgICAgICAgICAgICAgICAnKkNhcmEgUGFrZSBSU1ZQIGRpIEludml0ZSs6KlxcbicsXG4gICAgICAgICAgICAgICAgICAgICAgICAnMS4gQnVrYSBhcGxpa2FzaSBJbnZpdGUrXFxuJyxcbiAgICAgICAgICAgICAgICAgICAgICAgICcyLiBQaWxpaCB1bmRhbmdhbm11XFxuJyxcbiAgICAgICAgICAgICAgICAgICAgICAgICczLiBBa3RpZmthbiBmaXR1ciBSU1ZQXFxuJyxcbiAgICAgICAgICAgICAgICAgICAgICAgICc0LiBDdXN0b20gcGVydGFueWFhbiBrYWxhdSBwZXJsdVxcbicsXG4gICAgICAgICAgICAgICAgICAgICAgICAnNS4gVHVuZ2d1IGtvbmZpcm1hc2kgZGFyaSB0YW11IHlhbmcgYW50dXNpYXMhXFxuXFxuJyxcblxuICAgICAgICAgICAgICAgICAgICAgICAgJ1x1RDgzRVx1REQyOSpNYXUgdGF1IGxlYmloIGJhbnlhayBhdGF1IGxhbmdzdW5nIHBha2UgZml0dXIgaW5pPyBDaGF0IGtpdGEgZGkgc2luaSwga2l0YSBzaWFwIGJhbnR1ISogXHVEODNEXHVERTgwXHVEODNEXHVEQ0YyJyxcbiAgICAgICAgICAgICAgICAgICAgXTtcblxuICAgICAgICAgICAgICAgICAgICBjbGllbnQuc2VuZFRleHQobWVzc2FnZS5mcm9tLCByc3ZwLmpvaW4oJycpKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgZWxzZSBpZiAobWVzc2FnZT8ubGlzdFJlc3BvbnNlPy5zaW5nbGVTZWxlY3RSZXBseT8uc2VsZWN0ZWRSb3dJZCA9PT0gJ3RyYW5zYWtzaScpIHtcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgdHJhbnNha3NpID0gW1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJcdUQ4M0RcdURDQjggKkluZm9ybWFzaSBUcmFuc2Frc2kgZGkgSW52aXRlKyogXHVEODNEXHVERTgwXFxuXFxuXCIsXG5cbiAgICAgICAgICAgICAgICAgICAgICAgICdFaCwgZGVuZ2VyLWRlbmdlciBrYW11IG1hdSB0YXUgbmloIGdpbWFuYSBjZXJpdGFueWEgdXJ1c2FuIHRyYW5zYWtzaSBkaSBJbnZpdGUrPyBUZW5hbmcsIGtpdGEgcHVueWEgc2VtdWEgamF3YWJhbm55YSEgXHVEODNEXHVEQ0IwXHUyNzI4XFxuXFxuJyxcblxuICAgICAgICAgICAgICAgICAgICAgICAgJzFcdUZFMEZcdTIwRTMgKlBha2V0IExhbmdnYW5hbjoqXFxuJyxcbiAgICAgICAgICAgICAgICAgICAgICAgICcxLiBBZGEgYmViZXJhcGEgcGFrZXQgbGFuZ2dhbmFuIHlhbmcgYmlzYSBrYW11IHBpbGloXFxuJyxcbiAgICAgICAgICAgICAgICAgICAgICAgICcyLiBNdWxhaSBkYXJpIEJyb256ZSwgU2lsdmVyLCBzYW1wYWkgR29sZCwgc2VzdWFpIGtlYnV0dWhhbm11XFxuXFxuJyxcblxuICAgICAgICAgICAgICAgICAgICAgICAgJzJcdUZFMEZcdTIwRTMgKlVqaSBDb2JhIEdyYXRpczoqXFxuJyxcbiAgICAgICAgICAgICAgICAgICAgICAgICcxLiBOZ2dhayB1c2FoIGtoYXdhdGlyLCBiaXNhIGRpY29iYSBncmF0aXMgc2VsYW1hIDcgaGFyaSFcXG4nLFxuICAgICAgICAgICAgICAgICAgICAgICAgJzIuIEJpYXIga2FtdSBiaXNhIHRhdSBmaXR1cm55YSBzZWJlbHVtIGJlbmVyLWJlbmVyIGxhbmdnYW5hblxcblxcbicsXG5cbiAgICAgICAgICAgICAgICAgICAgICAgICczXHVGRTBGXHUyMEUzICpNZXRvZGUgUGVtYmF5YXJhbjoqXFxuJyxcbiAgICAgICAgICAgICAgICAgICAgICAgICcxLiBUZXJpbWEgcGVtYmF5YXJhbiBtZW5nZ3VuYWthbiBiZXJiYWdhaSBtZXRvZGVcXG4nLFxuICAgICAgICAgICAgICAgICAgICAgICAgJzIuIE11bGFpIGRhcmkgdHJhbnNmZXIgYmFuaywga2FydHUga3JlZGl0LCBzYW1wYWkgZS13YWxsZXQgZmF2b3JpdG11XFxuJyxcbiAgICAgICAgICAgICAgICAgICAgXTtcblxuICAgICAgICAgICAgICAgICAgICBjbGllbnQuc2VuZFRleHQobWVzc2FnZS5mcm9tLCB0cmFuc2Frc2kuam9pbignJykpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBlbHNlIGlmIChtZXNzYWdlPy5saXN0UmVzcG9uc2U/LnNpbmdsZVNlbGVjdFJlcGx5Py5zZWxlY3RlZFJvd0lkID09PSAncXInKSB7XG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IHFyID0gW1xuICAgICAgICAgICAgICAgICAgICAgICAgXCIqXHVEODNEXHVERDBEIFFSIEFic2VuIGRpIEludml0ZSsgXHVEODNDXHVERjEwKlxcblxcblwiLFxuXG4gICAgICAgICAgICAgICAgICAgICAgICAnTmloLCB5YW5nIGxhZ2kgbnlhcmkgY2FyYSBwcmFrdGlzIGJ1YXQgYWJzZW4gdGFtdSBkaSBhY2FyYSwgSW52aXRlKyBwdW55YSBzb2x1c2lueWEhIERlbmdhbiBRUiBBYnNlbiwgc2VtdWFueWEgamFkaSBsZWJpaCBjZXBhdCBkYW4gZWZpc2llbi4gXHVEODNEXHVERTgwXHUyNzI4XFxuXFxuJyxcblxuICAgICAgICAgICAgICAgICAgICAgICAgJzFcdUZFMEZcdTIwRTMgKkJhZ2FpbWFuYSBDYXJhIEtlcmphbnlhPypcXG4nLFxuICAgICAgICAgICAgICAgICAgICAgICAgJzEuIFBpbGloIGZpdHVyIFFSIEFic2VuIGRpIGFwbGlrYXNpIEludml0ZStcXG4nLFxuICAgICAgICAgICAgICAgICAgICAgICAgJzIuIEdlbmVyYXRlIFFSIENvZGUgdW5payB1bnR1ayBhY2FyYW11XFxuJyxcbiAgICAgICAgICAgICAgICAgICAgICAgICczLiBUYW11IGN1a3VwIHNjYW4gUVIgQ29kZSBpdHUgdW50dWsgYWJzZW5cXG5cXG4nLFxuXG4gICAgICAgICAgICAgICAgICAgICAgICAnMlx1RkUwRlx1MjBFMyAqS2VuYXBhIEhhcnVzIFBha2UgUVIgQWJzZW4/KlxcbicsXG4gICAgICAgICAgICAgICAgICAgICAgICAnMS4gUHJvc2VzIGFic2VuIGxlYmloIGNlcGF0IGRhbiB0YW5wYSByaWJldFxcbicsXG4gICAgICAgICAgICAgICAgICAgICAgICAnMi4gQWt1cmF0LCBrYXJlbmEgbGFuZ3N1bmcgdGVyY2F0YXQgZGkgc2lzdGVtXFxuXFxuJyxcblxuICAgICAgICAgICAgICAgICAgICAgICAgJzNcdUZFMEZcdTIwRTMgKlBhbnRhdSBLZWhhZGlyYW4gTGFuZ3N1bmc6KlxcbicsXG4gICAgICAgICAgICAgICAgICAgICAgICAnMS4gTGloYXQgbGl2ZSByZXBvcnQga2VoYWRpcmFuIHRhbXVcXG4nLFxuICAgICAgICAgICAgICAgICAgICAgICAgJzIuIFByYWt0aXMgYnVhdCBuZ2F0dXIgbG9naXN0aWsgYWNhcmFtdVxcblxcbicsXG5cbiAgICAgICAgICAgICAgICAgICAgICAgICdcdUQ4M0VcdUREMzMgKkNhcmEgUGFrZSBRUiBBYnNlbjoqXFxuJyxcbiAgICAgICAgICAgICAgICAgICAgICAgICcxLiBCdWthIGFwbGlrYXNpIEludml0ZStcXG4nLFxuICAgICAgICAgICAgICAgICAgICAgICAgJzIuIFBpbGloIGZpdHVyIFFSIEFic2VuXFxuJyxcbiAgICAgICAgICAgICAgICAgICAgICAgICczLiBHZW5lcmF0ZSBRUiBDb2RlXFxuJyxcbiAgICAgICAgICAgICAgICAgICAgICAgICc0LiBUYW11IHNjYW4gUVIgQ29kZSwgbGFuZ3N1bmcgdGVyY2F0YXQhXFxuJyxcbiAgICAgICAgICAgICAgICAgICAgXTtcblxuICAgICAgICAgICAgICAgICAgICBjbGllbnQuc2VuZFRleHQobWVzc2FnZS5mcm9tLCBxci5qb2luKCcnKSk7XG4gICAgICAgICAgICAgICAgfWVsc2V7XG4gICAgICAgICAgICAgICAgICAgIGFzeW5jIGZ1bmN0aW9uIHJ1bigpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnN0IGdlbkFJID0gbmV3IEdvb2dsZUdlbmVyYXRpdmVBSShBUElfS0VZKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnN0IG1vZGVsID0gZ2VuQUkuZ2V0R2VuZXJhdGl2ZU1vZGVsKHsgbW9kZWw6IE1PREVMX05BTUUgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnN0IGdlbmVyYXRpb25Db25maWcgPSB7XG4gICAgICAgICAgICAgICAgICAgICAgICB0ZW1wZXJhdHVyZTogMC45LFxuICAgICAgICAgICAgICAgICAgICAgICAgdG9wSzogMSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHRvcFA6IDEsXG4gICAgICAgICAgICAgICAgICAgICAgICBtYXhPdXRwdXRUb2tlbnM6IDIwNDgsXG4gICAgICAgICAgICAgICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBjb25zdCBzYWZldHlTZXR0aW5ncyA9IFtcbiAgICAgICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjYXRlZ29yeTogSGFybUNhdGVnb3J5LkhBUk1fQ0FURUdPUllfSEFSQVNTTUVOVCxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aHJlc2hvbGQ6IEhhcm1CbG9ja1RocmVzaG9sZC5CTE9DS19NRURJVU1fQU5EX0FCT1ZFLFxuICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjYXRlZ29yeTogSGFybUNhdGVnb3J5LkhBUk1fQ0FURUdPUllfSEFURV9TUEVFQ0gsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhyZXNob2xkOiBIYXJtQmxvY2tUaHJlc2hvbGQuQkxPQ0tfTUVESVVNX0FORF9BQk9WRSxcbiAgICAgICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY2F0ZWdvcnk6IEhhcm1DYXRlZ29yeS5IQVJNX0NBVEVHT1JZX1NFWFVBTExZX0VYUExJQ0lULFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRocmVzaG9sZDogSGFybUJsb2NrVGhyZXNob2xkLkJMT0NLX01FRElVTV9BTkRfQUJPVkUsXG4gICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNhdGVnb3J5OiBIYXJtQ2F0ZWdvcnkuSEFSTV9DQVRFR09SWV9EQU5HRVJPVVNfQ09OVEVOVCxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aHJlc2hvbGQ6IEhhcm1CbG9ja1RocmVzaG9sZC5CTE9DS19NRURJVU1fQU5EX0FCT1ZFLFxuICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgIF07XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHBhcnRzLnB1c2goe3RleHQ6ICdpbnB1dDogJytnZXRNZXNzYWdlfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnN0IHJlc3VsdCA9IGF3YWl0IG1vZGVsLmdlbmVyYXRlQ29udGVudCh7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY29udGVudHM6IFt7IHJvbGU6IFwidXNlclwiLCBwYXJ0cyB9XSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBnZW5lcmF0aW9uQ29uZmlnLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNhZmV0eVNldHRpbmdzLFxuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnN0IHJlc3BvbnNlID0gcmVzdWx0LnJlc3BvbnNlO1xuICAgICAgICAgICAgICAgICAgICAgICAgcGFydHMucHVzaCh7dGV4dDogcmVzcG9uc2UudGV4dCgpfSk7XG4gICAgICAgICAgICAgICAgICAgICAgICBjbGllbnQuc2VuZFRleHQobWVzc2FnZS5mcm9tLCByZXNwb25zZS50ZXh0KCkpO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgcnVuKCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGNvbnN0IHBhcmFtZXRlcjEgPSBwcm9jZXNzLmFyZ3ZbMl07XG5cbiAgICAgICAgICAgICAgICAvLyBHdW5ha2FuIHBhcmFtZXRlciBzZXN1YWkga2VidXR1aGFuXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ1BhcmFtZXRlciAxOicsIHBhcmFtZXRlcjEpO1xuICAgICAgICAgICAgfSBjYXRjaCAoZXJyb3IpIHtcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZyhlcnJvci5tZXNzYWdlKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfVxuXG5cbmV4cG9ydCBkZWZhdWx0IGRlZmluZUNvbmZpZyh7XG4gICAgcGx1Z2luczogW1xuICAgICAgICBsYXJhdmVsKHtcbiAgICAgICAgICAgIGlucHV0OiBbJ3Jlc291cmNlcy9jc3MvYXBwLmNzcycsICdyZXNvdXJjZXMvanMvYXBwLmpzJ10sXG4gICAgICAgICAgICByZWZyZXNoOiB0cnVlLFxuICAgICAgICB9KSxcbiAgICBdLFxufSk7XG4iXSwKICAibWFwcGluZ3MiOiAiO0FBQW1QLFNBQVMsb0JBQW9CO0FBQ2hSLE9BQU8sYUFBYTtBQUNwQixPQUFRLGdCQUFpQjtBQUN6QjtBQUFBLEVBQ0k7QUFBQSxFQUNBO0FBQUEsRUFDQTtBQUFBLE9BQ0c7QUFDUCxJQUFNLGFBQWE7QUFDbkIsSUFBTSxVQUFVO0FBRWhCLElBQU0sUUFBUTtBQUFBLEVBQ1YsRUFBQyxNQUFNLDJDQUEwQztBQUFBLEVBQ2pELEVBQUMsTUFBTSxnSEFBK0c7QUFBQSxFQUN0SCxFQUFDLE1BQU0saURBQWdEO0FBQUEsRUFDdkQsRUFBQyxNQUFNLCtEQUFnRTtBQUFBLEVBQ3ZFLEVBQUMsTUFBTSxzQ0FBcUM7QUFBQSxFQUM1QyxFQUFDLE1BQU0sb0VBQW1FO0FBQUEsRUFDMUUsRUFBQyxNQUFNLDJDQUEwQztBQUFBLEVBQ2pELEVBQUMsTUFBTSxxQkFBb0I7QUFBQSxFQUMzQixFQUFDLE1BQU0saUZBQWdGO0FBQUEsRUFDdkYsRUFBQyxNQUFNLHNJQUFxSTtB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
