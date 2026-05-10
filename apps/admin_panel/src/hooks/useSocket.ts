'use client'

import React, { createContext, useContext, useEffect, useState, useRef } from 'react'
import { io, Socket } from 'socket.io-client'

const SOCKET_URL = process.env.NEXT_PUBLIC_SOCKET_URL || 'http://localhost:3002'

interface SocketContextType {
    socket: Socket | null
    connected: boolean
}

const SocketContext = createContext<SocketContextType>({
    socket: null,
    connected: false,
})

export const SocketProvider = ({ children, namespace }: { children: React.ReactNode; namespace: string }) => {
    const [connected, setConnected] = useState(false)
    const socketRef = useRef<Socket | null>(null)

    useEffect(() => {
        const token = localStorage.getItem('wadexp_token')
        
        const socket = io(`${SOCKET_URL}${namespace}`, {
            auth: { token },
            transports: ['websocket'],
            autoConnect: true,
        })

        socket.on('connect', () => {
            console.log(`Socket connected to ${namespace}`)
            setConnected(true)
        })

        socket.on('disconnect', () => {
            console.log(`Socket disconnected from ${namespace}`)
            setConnected(false)
        })

        socketRef.current = socket

        return () => {
            socket.disconnect()
        }
    }, [namespace])

    return (
        <SocketContext.Provider value={{ socket: socketRef.current, connected }}>
            {children}
        </SocketContext.Provider>
    )
}

export const useSocket = () => useContext(SocketContext)
