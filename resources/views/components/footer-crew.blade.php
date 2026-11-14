<footer class="dcf mt-16"
        x-data="{
            talking: -1,
            hopping: -1,
            talkTimer: null,
            quotes: [
                'Siap jalan? Sabuk pengaman terpasang rapi!',
                'Koper siap di bagasi, saatnya berpetualang!',
                'Meow... jangan lupa bawa camilan di jalan ya.',
                'Tweet! Tiket eksekutif aman tanpa bentrok kursi.',
                'Tiket Anda siap diperiksa, selamat menikmati perjalanan!'
            ],
            poke(index) {
                this.talking = index;
                this.hopping = index;
                clearTimeout(this.talkTimer);
                this.talkTimer = setTimeout(() => {
                    this.talking = -1;
                    this.hopping = -1;
                }, 3000);
            },
            scrollToTop() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }">

    <!-- The World: Stamped GOTIX Wordmark & Crew -->
    <div class="dcf-world">
        <div class="dcf-stage">
            <h2 class="dcf-sr">GOTIX — Travel Crew</h2>

            <svg class="dcf-svg" viewBox="0 0 1000 320" role="img" aria-label="GOTIX Travel Crew">
                <defs>
                    <!-- Hatching Pattern -->
                    <pattern id="gotix-hatch" width="6" height="6" patternUnits="userSpaceOnUse" patternTransform="rotate(40)">
                        <line x1="0" y1="0" x2="0" y2="6" stroke="#18181B" stroke-width="1.4" />
                    </pattern>

                    <!-- Dry-brush ink filter -->
                    <filter id="gotix-ink" x="-5%" y="-5%" width="110%" height="110%">
                        <feTurbulence type="fractalNoise" baseFrequency="0.04" numOctaves="3" result="noise" />
                        <feDisplacementMap in="SourceGraphic" in2="noise" scale="4" xChannelSelector="R" yChannelSelector="G" />
                    </filter>
                </defs>

                <!-- Ground Line with Grass & Pebbles -->
                <g class="dcf-ground">
                    <path d="M 20 280 Q 250 278 500 281 Q 750 279 980 280" />
                    <path d="M 120 280 l 3 -12 m 4 12 l -2 -9 M 340 280 l 4 -14 m 5 14 l -3 -10 M 680 280 l 4 -12 m 4 12 l -2 -9 M 860 280 l 3 -14 m 5 14 l -3 -11" />
                    <!-- Pebbles -->
                    <ellipse cx="220" cy="288" rx="4" ry="2.5" fill="#FFFFFF" stroke="#18181B" stroke-width="1.5" />
                    <ellipse cx="580" cy="289" rx="5" ry="3" fill="#FFFFFF" stroke="#18181B" stroke-width="1.5" />
                    <ellipse cx="800" cy="287" rx="4" ry="2.5" fill="#FFFFFF" stroke="#18181B" stroke-width="1.5" />
                </g>

                <!-- G — Pak Sopir (Character 0) -->
                <g class="dcf-glyph" transform="translate(60, 0)">
                    <text x="0" y="275" font-family="'Bricolage Grotesque', sans-serif" font-size="230" font-weight="900" fill="#18181B" filter="url(#gotix-ink)">G</text>
                    <!-- Driver Character on G -->
                    <g class="dcf-art dcf-hopper" :class="{ 'is-hop': hopping === 0 }" transform="translate(45, 95)" @click="poke(0)" style="cursor: pointer;">
                        <!-- Cap -->
                        <path d="M -18 -25 C -15 -38 15 -38 18 -25 Z" fill="#F59E0B" />
                        <path d="M -22 -24 L 22 -24" stroke="#18181B" stroke-width="3" />
                        <!-- Head -->
                        <circle cx="0" cy="-10" r="15" fill="#FFFFFF" />
                        <!-- Eyes & Smile -->
                        <circle cx="-5" cy="-12" r="2.5" class="k" />
                        <circle cx="5" cy="-12" r="2.5" class="k" />
                        <path d="M -5 -4 Q 0 1 5 -4" fill="none" stroke="#18181B" stroke-width="2" />
                    </g>
                    <rect class="dcf-hit" x="25" y="65" width="45" height="55" @click="poke(0)" />
                </g>

                <!-- O — Si Petualang (Character 1) -->
                <g class="dcf-glyph" transform="translate(240, 0)">
                    <text x="0" y="275" font-family="'Bricolage Grotesque', sans-serif" font-size="230" font-weight="900" fill="#18181B" filter="url(#gotix-ink)">O</text>
                    <!-- Traveler Sitting on top of O -->
                    <g class="dcf-art dcf-hopper" :class="{ 'is-hop': hopping === 1 }" transform="translate(90, 75)" @click="poke(1)" style="cursor: pointer;">
                        <!-- Backpack -->
                        <rect x="-24" y="-22" width="12" height="20" rx="3" fill="#E11D48" />
                        <!-- Body & Head -->
                        <circle cx="0" cy="-24" r="14" fill="#FFFFFF" />
                        <path d="M -12 -10 L 12 -10 L 8 10 L -8 10 Z" fill="#FFFFFF" />
                        <!-- Dangling Legs -->
                        <path d="M -5 10 L -6 26 M 5 10 L 6 26" fill="none" stroke="#18181B" stroke-width="3" />
                        <!-- Eyes & Glasses -->
                        <circle cx="-5" cy="-25" r="4" fill="none" stroke="#18181B" stroke-width="2" />
                        <circle cx="5" cy="-25" r="4" fill="none" stroke="#18181B" stroke-width="2" />
                        <path d="M -1 -25 L 1 -25" stroke="#18181B" stroke-width="2" />
                    </g>
                    <rect class="dcf-hit" x="65" y="45" width="50" height="65" @click="poke(1)" />
                </g>

                <!-- T — Kucing Terminal (Character 2) -->
                <g class="dcf-glyph" transform="translate(450, 0)">
                    <text x="0" y="275" font-family="'Bricolage Grotesque', sans-serif" font-size="230" font-weight="900" fill="#18181B" filter="url(#gotix-ink)">T</text>
                    <!-- Terminal Cat Leaning on T stem -->
                    <g class="dcf-art dcf-hopper" :class="{ 'is-hop': hopping === 2 }" transform="translate(10, 200)" @click="poke(2)" style="cursor: pointer;">
                        <!-- Cat Body -->
                        <ellipse cx="0" cy="40" rx="18" ry="24" fill="#FFFFFF" />
                        <!-- Cat Ears -->
                        <path d="M -14 16 L -18 -4 L -4 8 Z" fill="#FFFFFF" />
                        <path d="M 14 16 L 18 -4 L 4 8 Z" fill="#FFFFFF" />
                        <!-- Cat Face -->
                        <circle cx="0" cy="20" r="16" fill="#FFFFFF" />
                        <circle cx="-5" cy="18" r="2.2" class="k" />
                        <circle cx="5" cy="18" r="2.2" class="k" />
                        <path d="M -3 24 L 3 24 L 0 26 Z" class="k" />
                        <!-- Whiskers -->
                        <path d="M -9 22 L -18 20 M -9 25 L -18 26 M 9 22 L 18 20 M 9 25 L 18 26" stroke="#18181B" stroke-width="1.5" />
                        <!-- Tail -->
                        <path d="M -16 52 Q -30 50 -26 30" fill="none" stroke="#18181B" stroke-width="3" />
                    </g>
                    <rect class="dcf-hit" x="-15" y="190" width="50" height="85" @click="poke(2)" />
                </g>

                <!-- I — Burung Perjalanan (Character 3) -->
                <g class="dcf-glyph" transform="translate(630, 0)">
                    <text x="0" y="275" font-family="'Bricolage Grotesque', sans-serif" font-size="230" font-weight="900" fill="#18181B" filter="url(#gotix-ink)">I</text>
                    <!-- Bird Perched on top of I -->
                    <g class="dcf-art dcf-hopper" :class="{ 'is-hop': hopping === 3 }" transform="translate(25, 65)" @click="poke(3)" style="cursor: pointer;">
                        <!-- Bird Body -->
                        <path d="M -12 0 C -14 -12 -6 -20 4 -20 C 14 -20 18 -12 16 0 C 14 8 6 10 0 10 C -8 10 -12 6 -12 0 Z" fill="#FFFFFF" />
                        <!-- Beak -->
                        <path d="M 14 -8 L 24 -6 L 14 -2 Z" fill="#F59E0B" />
                        <!-- Eye -->
                        <circle cx="6" cy="-8" r="2.4" class="k" />
                        <!-- Party / Travel Cap -->
                        <path d="M -2 -18 L 4 -32 L 10 -17 Z" fill="#E11D48" />
                        <!-- Legs -->
                        <path d="M -2 10 L -2 16 M 4 10 L 4 16" stroke="#18181B" stroke-width="2" />
                    </g>
                    <rect class="dcf-hit" x="10" y="30" width="35" height="50" @click="poke(3)" />
                </g>

                <!-- X — Petugas Loket (Character 4) -->
                <g class="dcf-glyph" transform="translate(730, 0)">
                    <text x="0" y="275" font-family="'Bricolage Grotesque', sans-serif" font-size="230" font-weight="900" fill="#18181B" filter="url(#gotix-ink)">X</text>
                    <!-- Conductor Peeking From Behind X -->
                    <g class="dcf-art dcf-hopper" :class="{ 'is-hop': hopping === 4 }" transform="translate(170, 150)" @click="poke(4)" style="cursor: pointer;">
                        <!-- Head -->
                        <circle cx="0" cy="0" r="16" fill="#FFFFFF" />
                        <!-- Cap -->
                        <path d="M -16 -6 C -12 -18 12 -18 16 -6 Z" fill="#18181B" />
                        <rect x="-18" y="-6" width="36" height="4" rx="2" fill="#F59E0B" />
                        <!-- Eyes & Smile -->
                        <circle cx="-5" cy="2" r="2.2" class="k" />
                        <circle cx="5" cy="2" r="2.2" class="k" />
                        <path d="M -4 8 Q 0 12 4 8" fill="none" stroke="#18181B" stroke-width="2" />
                        <!-- Ticket in Hand -->
                        <rect x="14" y="6" width="16" height="22" rx="2" fill="#FEF08A" stroke="#18181B" stroke-width="1.8" transform="rotate(15 22 17)" />
                    </g>
                    <rect class="dcf-hit" x="150" y="130" width="45" height="50" @click="poke(4)" />
                </g>
            </svg>

            <!-- Speech Bubble Display -->
            <div class="dcf-bubbles" aria-live="polite">
                <template x-if="talking >= 0">
                    <div class="dcf-bubble"
                         :style="`left: ${[11, 33, 49, 65, 87][talking]}%; top: ${[38, 28, 62, 26, 48][talking]}%;`"
                         x-text="quotes[talking]">
                    </div>
                </template>
            </div>
        </div>

        <!-- Tagline Footer Gotix -->
        <div class="dcf-cta">
            <p class="dcf-tagline">
                Perjalanan antar-kota yang tenang, pasti dapat kursi, dan selalu tiba tepat waktu.
            </p>
        </div>
    </div>

    <!-- Bottom Bar -->
    <div class="dcf-bar">
        <p class="font-medium text-slate-800">
            &copy; {{ date('Y') }} GoTix Travel Indonesia. Tiket Terproteksi Anti Double-Booking.
        </p>

        <ul class="flex flex-wrap items-center gap-4 text-xs font-semibold ml-auto">
            <li><a href="{{ route('beranda') }}" class="dcf-link">Beranda</a></li>
            <li><a href="{{ route('jadwal.cari') }}" class="dcf-link">Jadwal</a></li>
            <li><a href="mailto:bantuan@gotix.id" class="dcf-link">Bantuan</a></li>
        </ul>

        <button type="button"
                @click="scrollToTop()"
                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border-2 border-slate-900 bg-white font-bold text-xs shadow-[2px_2px_0px_#18181b] hover:-translate-y-0.5 active:translate-y-0.5 transition">
            <span>Ke Atas</span>
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
            </svg>
        </button>
    </div>
</footer>
