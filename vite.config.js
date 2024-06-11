import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import  wppconnect  from '@wppconnect-team/wppconnect';
import {
    GoogleGenerativeAI,
    HarmCategory,
    HarmBlockThreshold,
} from "@google/generative-ai";
const MODEL_NAME = "gemini-1.0-pro";
const API_KEY = "AIzaSyBIsUZg9WfxbjgsehLjYkiWZ_k_hrEKrqQ";

const parts = [
    {text: "input: harga undangan digital di invite+"},
    {text: "output: Paket Bronze - Rp 25,000/bulan, Paket Silver - Rp 40,000/bulan sedangkan Paket Gold - Rp 60,000/bulan"},
    {text: "input: bagaimana cara memesan undangan digital"},
    {text: "output: ketik \"info\" untuk mendapatkan info lebih lengkapnya"},
    {text: "input: link alamat undangan digital"},
    {text: "output: anda dapat mengunjungi website berikut www.inviteplus.com"},
    {text: "input: apa nama website undangan digital"},
    {text: "output: inviteplus"},
    {text: "input: apakah ada video tutorial untuk menggunakan undangan digital di invite+"},
    {text: "output: anda dapat melihat video tutorial di channel youtube inviteplus atau dengan link berikut https://www.youtube.com/inviteplus"},
    {text: "input: metode pembayaran apa saja yang bisa digunakan untuk pembayaran inviteplus"},
    {text: "output: Mulai dari transfer bank, kartu kredit, sampai e-wallet favoritmu pada midtrans"},
    {text: "input: apakah layanan ini menyediakan fitur gratis / free"},
    {text: "output: layanan inviteplus menyediakan layanan versi gratis untuk pengguna"},
    {text: "input: apa saja yang didapat pada layanan versi gratis / free pada undangan inviteplus"},
    {text: "output: anda dapat melihat detail info fitur dan keunggulan setiap layanan pada website resmi kami di www.inviteplus.com"},
  ];

wppconnect
    .create()
    .then((client) => start(client))
    .catch((error) => console.log(error));

    function start(client) {
        client.onMessage(async (message) => {
            const getMessage = message.body;
            const command = getMessage.split(' ');
            try {
                if (command[0] === '>sticker') {
                    await client
                        .sendImageAsSticker(message.from, command[1])
                        .then((result) => {
                            console.log('Result: ', result); //return object success
                        })
                        .catch((erro) => {
                            console.error('Error when sending: ', erro); //return object error
                        });
                }
                else if (command[0] === 'info') {
                    const welcome = [
                        "🎉 Halo di Invite+! 🌈",
                        "Seneng banget kamu milih Invite+ buat undangan digital kamu! 🚀 Yuk, bareng-bareng kita bikin momen spesial jadi makin memorable. 🌟",
                        "Dengan aplikasi Invite+, bikin undangan jadi gampang dan seru, loh! 🎈 Kalo ada pertanyaan ato butuh bantuan, langsung aja chat kita di sini ya. Kita siap nolongin kamu! 🤗",
                        "Makasih banyak udah percaya sama kita! Selamat merencanain acaramu, semoga undangan digital dari Invite+ bener-bener ngebuat momen istimewa jadi lebih istimewa lagi! 🎊✨"
                    ];
                    client.sendListMessage(message.from, {
                        buttonText: 'Pilih opsi pertanyaan disini',
                        description: welcome.join('\n\n'),
                        sections: [
                        {
                            title: 'Informasi Undangan',
                            rows: [
                            {
                                rowId: 'harga',
                                title: 'Harga Paket Langganan',
                                description: 'Berapa harga undangan digital yang ditawarkan dan apa saja yang termasuk dalam paketnya?',
                            },
                            {
                                rowId: 'tema',
                                title: 'Desain dan Tema',
                                description: 'Apakah ada pilihan desain atau tema khusus untuk undangan digital?',
                            },
                            {
                                rowId: 'rsvp',
                                title: 'RSVP',
                                description: 'Apakah undangan digital ini mendukung fitur RSVP (konfirmasi kehadiran)?',
                            },
                            ],
                        },
                        {
                            title: 'Cara Menggunakan Undangan',
                            rows: [
                                {
                                    rowId: 'transaksi',
                                    title: 'Cara Pembayaran Paket Langganan',
                                    description: 'Bagaimana cara melakukan pembayaran paket langganan ?',
                                },
                                {
                                    rowId: 'qr',
                                    title: 'Cara konfirmasi Kehadiran dengan QR',
                                    description: 'Bagaimana cara melakukan konfirmasi kehadiran melalui QR ?',
                                },
                                {
                                    rowId: 'tutorial',
                                    title: 'Tutorial Penggunaan Aplikasi',
                                    description: 'apakah ada tutorial video penggunaan aplikasi undangan digital ?',
                                },
                            ],
                        },
                        ],
                    });
                }
                else if (message?.listResponse?.singleSelectReply?.selectedRowId === 'harga') {
                    const harga = [
                        "🌟*Harga Langganan di Invite+ 🌟* \n\n",
                        'Yo, teman-teman Invite+! Ini nih info harga langganan buat bikin undangan digital kece bareng kita! 💸✨ \n\n',

                        '1️⃣ *Paket Bronze - Rp 25,000/bulan:*\n',
                        '1. Desain undangan keren\n',
                        '2. Manajemen RSVP yang mudah\n',
                        '3. Distribusi lewat WhatsApp\n\n',

                        '2️⃣ *Paket Silver - Rp 40,000/bulan:*\n',
                        '1. Semua fitur di Paket Bronze\n',
                        '2. Kustomisasi tambahan (warna, font, dll.)\n',
                        '3. Integrasi dengan platform lain\n\n',

                        '3️⃣ *Paket Gold - Rp 60,000/bulan:*\n',
                        '1. Semua fitur di Paket Silver\n',
                        '2. Keamanan dan privasi premium\n',
                        '3. Support pelanggan 24/7\n',

                        '💡 *Catatan Penting:*\n',
                        'Semua paket bisa dicoba GRATIS selama 7 hari. Harga sudah termasuk pajak.\n\n',
                        '*Mau tanya-tanya atau mau mulai langganan? Langsung aja chat kita di sini. Kita siap ngebantu kamu!*🚀👋',
                    ];

                    client.sendText(message.from, harga.join(''));
                }
                else if (message?.listResponse?.singleSelectReply?.selectedRowId === 'tema') {
                    const tema = [
                        "🎨 *Temakan Undangan di Invite+* 🌈",

                        'Eh, udah tau gak nih, di Invite+ kita punya teman-teman undangan yang keren abis buat acaramu? 🌟✨',

                        'Langsung aja lihat thema undangan di website Invite+. Kita siap bantuin mewujudkan undangan impian kamu! 🌈💌',
                    ];

                    client.sendText(message.from, tema.join('\n\n'));
                }
                else if (message?.listResponse?.singleSelectReply?.selectedRowId === 'rsvp') {
                    const rsvp = [
                        "📆 *RSVP di Invite+* 🎉\n\n",

                        'Wah, undangan aja udah kece, tapi RSVP-nya di Invite+ lebih kece lagi! Yuk, kenalan sama fiturnya. 🌟✨\n\n',

                        '*Cara Pake RSVP di Invite+:*\n',
                        '1. Buka aplikasi Invite+\n',
                        '2. Pilih undanganmu\n',
                        '3. Aktifkan fitur RSVP\n',
                        '4. Custom pertanyaan kalau perlu\n',
                        '5. Tunggu konfirmasi dari tamu yang antusias!\n\n',

                        '🤩*Mau tau lebih banyak atau langsung pake fitur ini? Chat kita di sini, kita siap bantu!* 🚀📲',
                    ];

                    client.sendText(message.from, rsvp.join(''));
                }
                else if (message?.listResponse?.singleSelectReply?.selectedRowId === 'transaksi') {
                    const transaksi = [
                        "💸 *Informasi Transaksi di Invite+* 🚀\n\n",

                        'Eh, denger-denger kamu mau tau nih gimana ceritanya urusan transaksi di Invite+? Tenang, kita punya semua jawabannya! 💰✨\n\n',

                        '1️⃣ *Paket Langganan:*\n',
                        '1. Ada beberapa paket langganan yang bisa kamu pilih\n',
                        '2. Mulai dari Bronze, Silver, sampai Gold, sesuai kebutuhanmu\n\n',

                        '2️⃣ *Uji Coba Gratis:*\n',
                        '1. Nggak usah khawatir, bisa dicoba gratis selama 7 hari!\n',
                        '2. Biar kamu bisa tau fiturnya sebelum bener-bener langganan\n\n',

                        '3️⃣ *Metode Pembayaran:*\n',
                        '1. Terima pembayaran menggunakan berbagai metode\n',
                        '2. Mulai dari transfer bank, kartu kredit, sampai e-wallet favoritmu\n',
                    ];

                    client.sendText(message.from, transaksi.join(''));
                }
                else if (message?.listResponse?.singleSelectReply?.selectedRowId === 'qr') {
                    const qr = [
                        "*🔍 QR Absen di Invite+ 🌐*\n\n",

                        'Nih, yang lagi nyari cara praktis buat absen tamu di acara, Invite+ punya solusinya! Dengan QR Absen, semuanya jadi lebih cepat dan efisien. 🚀✨\n\n',

                        '1️⃣ *Bagaimana Cara Kerjanya?*\n',
                        '1. Pilih fitur QR Absen di aplikasi Invite+\n',
                        '2. Generate QR Code unik untuk acaramu\n',
                        '3. Tamu cukup scan QR Code itu untuk absen\n\n',

                        '2️⃣ *Kenapa Harus Pake QR Absen?*\n',
                        '1. Proses absen lebih cepat dan tanpa ribet\n',
                        '2. Akurat, karena langsung tercatat di sistem\n\n',

                        '3️⃣ *Pantau Kehadiran Langsung:*\n',
                        '1. Lihat live report kehadiran tamu\n',
                        '2. Praktis buat ngatur logistik acaramu\n\n',

                        '🤳 *Cara Pake QR Absen:*\n',
                        '1. Buka aplikasi Invite+\n',
                        '2. Pilih fitur QR Absen\n',
                        '3. Generate QR Code\n',
                        '4. Tamu scan QR Code, langsung tercatat!\n',
                    ];

                    client.sendText(message.from, qr.join(''));
                }else{
                    async function run() {
                        const genAI = new GoogleGenerativeAI(API_KEY);
                        const model = genAI.getGenerativeModel({ model: MODEL_NAME });

                        const generationConfig = {
                        temperature: 0.9,
                        topK: 1,
                        topP: 1,
                        maxOutputTokens: 2048,
                        };

                        const safetySettings = [
                        {
                            category: HarmCategory.HARM_CATEGORY_HARASSMENT,
                            threshold: HarmBlockThreshold.BLOCK_MEDIUM_AND_ABOVE,
                        },
                        {
                            category: HarmCategory.HARM_CATEGORY_HATE_SPEECH,
                            threshold: HarmBlockThreshold.BLOCK_MEDIUM_AND_ABOVE,
                        },
                        {
                            category: HarmCategory.HARM_CATEGORY_SEXUALLY_EXPLICIT,
                            threshold: HarmBlockThreshold.BLOCK_MEDIUM_AND_ABOVE,
                        },
                        {
                            category: HarmCategory.HARM_CATEGORY_DANGEROUS_CONTENT,
                            threshold: HarmBlockThreshold.BLOCK_MEDIUM_AND_ABOVE,
                        },
                        ];

                        parts.push({text: 'input: '+getMessage});

                        const result = await model.generateContent({
                            contents: [{ role: "user", parts }],
                            generationConfig,
                            safetySettings,
                        });

                        const response = result.response;
                        parts.push({text: response.text()});
                        client.sendText(message.from, response.text());
                    }

                    run();
                }
                const parameter1 = process.argv[2];

                // Gunakan parameter sesuai kebutuhan
                console.log('Parameter 1:', parameter1);
            } catch (error) {
                console.log(error.message);
            }
        });
    }


export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
