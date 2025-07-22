<div
  wire:poll.5000ms="refreshAll"
  x-data="{
    // ONLY the top (called + video) lives here now:
    called: @entangle('calledTicket'),
    calledCardAnimation: '',
    playlist: JSON.parse($refs.playlistData.textContent || '[]'),
    idx: 0,
    initPlaylist() {
      window.tv = this;
      this.playNext();
    },
    playNext() {
      if (!this.playlist.length) return;
      const v = this.playlist[this.idx];
      this.idx = (this.idx + 1) % this.playlist.length;
      $refs.player.src = v.url;
      this.$dispatch('video-playing', { videoId: v.id });
      this.$refs.player.play().catch(() => this.playNext());
    }
  }"
  x-init="initPlaylist()"
  style="display: flex; flex-direction: column; height: 100vh;"
>

  {{-- TOP: Called ticket + Video --}}
  <div style="flex:1; display:flex; overflow:hidden;">
    {{-- Called Ticket --}}
    <div style="flex:2; background:#111; display:flex;align-items:center;justify-content:center;padding:2rem;">
      <template x-if="called">
        <div
          x-show="called"
          x-transition:enter="transition ease-out duration-500"
          x-transition:enter-start="opacity-0 scale-95"
          x-transition:enter-end="opacity-100 scale-100"
          :class="calledCardAnimation"
          class="called-card bg-warning text-dark text-center p-5 rounded shadow"
          style="width: 100%; max-width: 90%;"
        >
          <h1 class="fw-bold mb-4" style="font-weight: 800; font-size: 5rem; display: flex; gap: 1rem; justify-content: center;">
            <span x-text="called.area.codigo_area"></span>
            <span>-</span>
            <span x-text="String(called.numero).padStart(3, '0')"></span>
          </h1>
          <h2 class="fw-semibold" style= "font-weight: 800;">Pase a:</h2>
          <h3 class="fw-bold" style="font-weight: 700;" x-text="called.escritorio.nombre_escritorio"></h3>
        </div>

      </template>
      <template x-if="!called">
        <h2 class="text-white">Esperando llamada 📣</h2>
      </template>
    </div>
    {{-- Video --}}
    <div style="flex:5.8; background:#000; padding:1rem;">
      <div style="width:100%;height:100%;position:relative;overflow:hidden;border-radius:.5rem;">
        <div x-ref="playlistData" style="display:none;">
          {!! json_encode(
              \App\Models\Video::orderBy('uploaded_at')->get()->map(fn($v)=>[
                'id'=>$v->id,'url'=>asset('storage/'.$v->ruta_archivo)
              ])
          ) !!}
        </div>
        <video
          x-ref="player"
          autoplay muted
          @ended="playNext()"
          style="width:100%;height:100%;object-fit:cover;"
        ></video>
      </div>
    </div>
  </div>

{{-- BOTTOM SECTION: Tickets List --}}
<div
  x-data="{ tickets: @entangle('nextTickets') || [] }"
  style="flex: 0.25; background-color: #e9e9e9; padding: 0.75rem 1rem; display: flex; flex-direction: column; overflow: hidden;"
>
  <h5 class="fw-bold mb-1" style="color: #222; margin-bottom: 0;">En espera</h5>
  <div style="margin-top: -15px; flex: 1; display: flex; flex-direction: row; align-items: center; justify-content: center; gap: 1rem; overflow-x: auto; overflow-y: hidden;">
    <template x-for="ticket in tickets" :key="ticket.id">
      <div
        style="
          min-width: 240px;
          flex: 0 0 auto;
          background-color: #fff;
          color: #000;
          padding: 1.5rem;
          border-radius: 1rem;
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          text-align: center;
          font-family: sans-serif;
          box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        "
      >
        <div style="font-size: 2.25rem; font-weight: 800;">
          <span x-text="`${ticket.area.codigo_area} - ${String(ticket.numero).padStart(3, '0')}`"></span>
        </div>
        <div style="font-size: 1.25rem; font-weight: 600; margin-top: 0.75rem;" x-show="ticket.escritorio?.nombre_escritorio">
          <span x-text="ticket.escritorio.nombre_escritorio"></span>
        </div>
      </div>
    </template>

    <!-- only check length once tickets is defined -->
    <template x-if="tickets && tickets.length === 0">
      <div
        class="d-flex align-items-center justify-content-center text-center shadow"
        style="min-width: 220px; flex: 0 0 auto; background-color: #ddd; color: #333; font-size: 1.25rem; padding: 1rem; border-radius: 1rem;"
      >
        No hay tickets en espera
      </div>
    </template>
  </div>
</div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const bipSound = document.getElementById('bip-sound');
    document.body.addEventListener('click', () => {
        if (bipSound) bipSound.volume = 0.5;
    }, { once: true });

    window.tv = window.tv || {};
    const callQueue = [];
    let processing = false;

    function enqueueCall(payload) {
        callQueue.push(payload);
        processNext();
    }

    async function processNext() {
        if (processing || !callQueue.length) return;

        processing = true;
        const payload = callQueue.shift();
        //console.log('Processing call for ticket:', payload);

        // Wait 2 seconds before triggering
        await new Promise(res => setTimeout(res, 2000));

        // 1. Update “called” card
        window.tv.called = {
            id: payload.ticketId,
            numero: payload.code,
            area: { codigo_area: payload.areaCode },
            escritorio: { nombre_escritorio: payload.desk }
        };

        // 2. Update ticket history
        const previous = window.tv.calledHistory || [];
        let arr = (previous.filter(t => t.id !== payload.ticketId));
        if (previous.length && previous[0].id !== payload.ticketId) {
            arr.unshift(previous[0]);
        }
        window.tv.calledHistory = arr.slice(0, 5);
        window.tv.tickets = window.tv.calledHistory;

        // 3. Play beep
        if (bipSound) {
            bipSound.currentTime = 0;
            try { await bipSound.play(); } catch (e) {
                console.error('Beep error:', e);
            }
        }

        // 4. Flicker
        const calledCard = document.querySelector('.called-card');
        if (calledCard) {
            calledCard.classList.remove('flicker');
            void calledCard.offsetWidth;
            calledCard.classList.add('flicker');
        }

        // 5. TTS
        const text = `${payload.areaCode}, ${payload.code}, pase ${payload.desk}`;
        try {
            const response = await fetch('/api/tts', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'audio/mpeg',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ text })
            });
            if (response.ok) {
                const audioData = await response.blob();
                const audio = new Audio(URL.createObjectURL(audioData));
                await new Promise((resolve) => {
                    audio.onended = resolve;
                    audio.onerror = resolve;
                    audio.play();
                });
            }
        } catch (err) {
            console.error('TTS error:', err);
        }

        // Optional: wait 1 more second after TTS
        await new Promise(res => setTimeout(res, 1000));

        processing = false;
        processNext();
    }

    if (window.Echo) {
        window.Echo.channel('tv-display')
            .listen('.TicketCalled', (payload) => {
                //console.log('TicketCalled event received', payload);
                enqueueCall(payload);
            })
            .listen('.NewVideoUploaded', (payload) => {
                //console.log('NewVideoUploaded → reload TV', payload);
                window.location.relo  ad();
            })
            .listen('.VideoDeleted', () => {
                //console.log('VideoDeleted → reload TV');
                window.location.reload();
            });
    }
});
</script>
@endpush