import { computed, watch, inject } from 'vue'
import { usePage } from '@inertiajs/vue3'

let stopEcho: (() => void) | null = null
let activeUserId: number | null = null

export function useUserChannelSingleton(
    callback: (eventData: any) => void
) {
    const page = usePage()
    const echo = inject<any>('echo')

    const userId = computed(() => page.props.auth?.user?.id ?? null)

    watch(
        userId,
        (id) => {
            // user logged out or changed
            if (!id) {
                stopEcho?.()
                stopEcho = null
                activeUserId = null
                return
            }

            // already subscribed
            if (activeUserId === id) {
                return
            }

            // cleanup old
            stopEcho?.()

            console.log(`Subscribing to private channel: user.${id}`)

            // Try injected echo, fallback to window.Echo
            const echoInstance = echo || (window as any).Echo

            if (echoInstance) {
                const channel = echoInstance.private(`user.${id}`)

                channel.listen('.OrderMatched', callback)

                stopEcho = () => {
                    console.log(`Leaving channel: user.${id}`)
                    echoInstance.leave(`user.${id}`)
                }

                console.log('Subscribed to user channel:', id)
                activeUserId = id
            } else {
                console.error('Echo instance not found via inject or window')
            }
        },
        { immediate: true }
    )
}
