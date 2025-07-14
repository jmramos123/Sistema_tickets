<div
  wire:poll.5000ms="refreshAll"
  x-data="{
    tickets: @entangle('nextTickets'),
    called: @entangle('calledTicket'),
    calledCardAnimation: ''
  }"
  style="display: flex; flex-direction: column; height: 100vh; overflow: hidden;"
>

  {{-- TOP SECTION --}}
  <div style="display: flex; flex: 1; overflow: hidden;">

    {{-- Called Ticket --}}
    <div style="flex: 2; background: #111; display: flex; align-items: center; justify-content: center; padding: 2rem;">
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
          <h1 class="display-1 fw-bold mb-4">#<span x-text="called.numero"></span></h1>
          <h2 class="fw-semibold">Diríjase a:</h2>
          <h3 class="fw-bold" x-text="called.escritorio.nombre_escritorio"></h3>
        </div>
      </template>
      <template x-if="!called">
        <h2 class="fw-bold text-white">Esperando llamada 📣</h2>
      </template>
    </div>

    {{-- Video --}}
    <div style="flex: 5.8; background: #000; padding: 1rem;">
      <div style="flex: 1; border-radius: 0.5rem; overflow: hidden;">
        @if($activeVideo)
          <video autoplay loop muted style="width: 100%; height: 100%; object-fit: cover;">
            <source src="{{ asset('storage/'.$activeVideo->ruta_archivo) }}" type="video/mp4">
          </video>
        @else
          <div class="d-flex justify-content-center align-items-center bg-secondary rounded" style="width: 100%; height: 100%;">
            <h5 class="text-white m-0">No hay video 📺</h5>
          </div>
        @endif
      </div>
    </div>

  </div>

    {{-- BOTTOM SECTION: Tickets List --}}
    <div style="flex: 0.25; background-color: #e9e9e9; padding: 0.75rem 1rem 0.75rem 1rem; display: flex; flex-direction: column; overflow: hidden;">
    <h5 class="fw-bold mb-1" style="color: #222; margin-bottom: 0;">En espera</h5>
        <div style="margin-top: -15px; flex: 1; display: flex; flex-direction: row; align-items: center; justify-content: center; gap: 1rem; overflow-x: auto; overflow-y: hidden;">
            <template x-for="ticket in tickets" :key="ticket.id">
            <div 
                class="ticket-card shadow"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2">
                <span x-text="`${ticket.area.codigo_area} - ${ticket.numero}`" style="line-height: 1;"></span>
            </div>
            </template>
            <template x-if="tickets.length === 0">
            <div class="d-flex align-items-center justify-content-center text-center shadow"
                style="min-width: 220px; flex: 0 0 auto; background-color: #ddd; color: #333; font-size: 1.25rem; padding: 1rem; border-radius: 1rem;">
                No hay tickets en espera
            </div>
            
            </template>
        </div>
    </div>

</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Initialize the beep sound
    const bipSound = document.getElementById('bip-sound');
    let lastProcessedTicketId = null;
    
    // Set sound volume on first interaction
    document.body.addEventListener('click', () => {
        if (bipSound) bipSound.volume = 0.5;
    }, { once: true });

    // Single Echo listener for all ticket call events
    window.Echo.channel('tv-display')
        .listen('.TicketCalled', (payload) => {
            console.log('TicketCalled event received', payload);
            
            // Deduplicate by ticket ID
            if (payload.ticketId && payload.ticketId === lastProcessedTicketId) {
                console.log('Duplicate ticket call detected, skipping');
                return;
            }
            lastProcessedTicketId = payload.ticketId;
            
            // Play beep sound
            if (bipSound) {
                bipSound.currentTime = 0;
                bipSound.play().catch(e => console.error('Beep error:', e));
            }
            
            // Trigger flicker effect
            const calledCard = document.querySelector('.called-card');
            if (calledCard) {
                calledCard.classList.remove('flicker');
                void calledCard.offsetWidth;
                calledCard.classList.add('flicker');
            }
            
            // Handle TTS with debouncing
            if (payload.areaCode && payload.code && payload.desk) {
                const text = `${payload.areaCode}, ${payload.code}, pase a escritorio ${payload.desk}`;
                handleTTS(text);
            }
        });

    // TTS handler with debouncing
    let ttsCooldown = false;
    const handleTTS = async (text) => {
        if (ttsCooldown) {
            console.log('TTS cooldown active, skipping');
            return;
        }
        
        ttsCooldown = true;
        console.log('Processing TTS for:', text);
        
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
                new Audio(URL.createObjectURL(audioData)).play();
            }
        } catch (err) {
            console.error('TTS error:', err);
        } finally {
            setTimeout(() => {
                ttsCooldown = false;
            }, 2000); // 2-second cooldown
        }
    };
});
</script>
@endpush