<script @if (\Illuminate\Support\Facades\Vite::cspNonce()) nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}" @endif>
    (() => {
        // Livewire's wire:navigate runs inline scripts again on every visit.
        if (window.uatBubble) {
            return
        }

        window.uatBubble = true

        const storageKey = 'uat-bubble-open'
        const bubble = () => document.getElementById('uat-bubble')

        const isOpen = () => {
            try {
                return window.localStorage.getItem(storageKey) === 'true'
            } catch {
                return false
            }
        }

        const remember = (open) => {
            try {
                window.localStorage.setItem(storageKey, String(open))
            } catch {
                // Storage can be blocked, the panel then closes on every page load.
            }
        }

        const show = (open) => {
            if (! bubble()) {
                return
            }

            bubble().querySelector('[data-uat-panel]').hidden = ! open
            bubble().querySelector('[data-uat-toggle]').setAttribute('aria-expanded', String(open))
        }

        const report = (message) => {
            const element = bubble().querySelector('[data-uat-message]')

            element.textContent = message
            element.hidden = false
        }

        const request = async (url, options = {}) => {
            const response = await fetch(url, {
                credentials: 'same-origin',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                ...options,
            })

            if ([401, 404, 419].includes(response.status)) {
                bubble().remove()

                return
            }

            if (response.status === 403) {
                return request(bubble().dataset.uatUrl)
            }

            if (response.status === 422) {
                const body = await response.json()

                return report(body.message)
            }

            if (! response.ok) {
                return report(bubble().dataset.uatError)
            }

            bubble().innerHTML = await response.text()
            show(true)
        }

        const submit = async (form, submitter) => {
            if (bubble().getAttribute('aria-busy') === 'true') {
                return
            }

            const data = new FormData(form, submitter)
            const url = new URL(form.action)

            bubble().setAttribute('aria-busy', 'true')

            try {
                if (form.method === 'get') {
                    url.search = new URLSearchParams(data).toString()

                    await request(url)
                } else {
                    data.set('page_url', window.location.href)
                    data.set('viewport_width', window.innerWidth)
                    data.set('viewport_height', window.innerHeight)

                    await request(url, { method: 'POST', body: data })
                }
            } catch {
                report(bubble().dataset.uatError)
            } finally {
                bubble()?.removeAttribute('aria-busy')
            }
        }

        document.addEventListener('submit', (event) => {
            if (! event.target.closest('#uat-bubble')) {
                return
            }

            event.preventDefault()
            submit(event.target, event.submitter)
        })

        document.addEventListener('click', (event) => {
            if (event.target.closest('#uat-bubble [data-uat-toggle]')) {
                remember(! isOpen())
                show(isOpen())
            }

            if (event.target.closest('#uat-bubble [data-uat-hide]')) {
                bubble().remove()
            }
        })

        document.addEventListener('input', (event) => {
            if (! event.target.matches('#uat-bubble [data-uat-search]')) {
                return
            }

            const term = event.target.value.toLowerCase()

            bubble().querySelectorAll('[data-uat-scenario]').forEach((scenario) => {
                scenario.hidden = ! scenario.textContent.toLowerCase().includes(term)
            })
        })

        document.addEventListener('livewire:navigated', () => show(isOpen()))

        show(isOpen())
    })()
</script>
