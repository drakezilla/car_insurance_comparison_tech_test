<script setup lang="ts">
import { onUnmounted, ref } from 'vue'

type StreamEvent =
  | {
      type: 'result'
      service: string
      url: string
      completed_after_seconds: number
      status: number | null
      error: string | null
    }
  | { type: 'completed'; total_seconds: number }

const events = ref<StreamEvent[]>([])
const loading = ref(false)
const status = ref('Ready')
let controller: AbortController | undefined

async function loadResults() {
  events.value = []
  loading.value = true
  status.value = 'Loading…'
  controller = new AbortController()

  try {
    const response = await fetch('/concurrent/requests', {
      signal: controller.signal,
      headers: { Accept: 'application/x-ndjson' },
    })
    if (!response.ok) throw new Error(`HTTP error: ${response.status}`)
    if (!response.body) throw new Error('Response body is unavailable')

    const reader = response.body.getReader()
    const decoder = new TextDecoder()
    let buffer = ''
    let completed = false

    try {
      while (true) {
        const { value, done } = await reader.read()
        buffer += done ? decoder.decode() : decoder.decode(value, { stream: true })

        let newline: number
        while ((newline = buffer.indexOf('\n')) !== -1) {
          const line = buffer.slice(0, newline)
          buffer = buffer.slice(newline + 1)
          const event: StreamEvent = JSON.parse(line)
          if (completed) throw new Error('Received data after completion')
          if (event.type !== 'result' && event.type !== 'completed') {
            throw new Error('Unknown event type')
          }
          events.value.push(event)
          if (event.type === 'completed') completed = true
        }

        if (done) break
      }
      if (buffer || !completed) throw new Error('Connection closed before completion')
      status.value = 'Completed'
    } finally {
      await reader.cancel().catch(() => {})
      reader.releaseLock()
    }
  } catch (error) {
    status.value = error instanceof Error ? error.message : 'Request failed'
  } finally {
    loading.value = false
  }
}

onUnmounted(() => controller?.abort())
</script>

<template>
  <h1>Concurrent requests</h1>
  <button :disabled="loading" @click="loadResults">Load results</button>
  <p role="status">{{ status }}</p>
  <ul aria-live="polite">
    <li v-for="(event, index) in events" :key="index">
      <pre>{{ JSON.stringify(event, null, 2) }}</pre>
    </li>
  </ul>
</template>
