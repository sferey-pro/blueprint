import React, { useState, useEffect } from 'react';
import axios from 'axios';
import DataTable from 'react-data-table-component';

const columns = [
    {
        name: 'ID',
        selector: row => row.id,
        sortable: true,
        width: '300px'
    },
    {
        name: 'Message',
        selector: row => row.message,
        sortable: true,
    },
    {
        name: 'Status',
        selector: row => row.status,
        sortable: true,
    },
    {
        name: 'Créé le',
        selector: row => row.createdAt,
        sortable: true,
    },
];

export default function GreetingList({ endpoint }) {
    const [data, setData] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        axios.get(endpoint)
            .then(res => {
                setData(res.data);
                setLoading(false);
            })
            .catch(error => {
                console.error("Erreur lors du chargement initial des données:", error);
                setLoading(false);
            });
    }, [endpoint]);

    useEffect(() => {
        const topicUrl = 'https://localhost/greetings-notify';
        const hubUrl = new URL('https://localhost/.well-known/mercure');
        hubUrl.searchParams.append('topic', topicUrl);

        const eventSource = new EventSource(hubUrl);

        eventSource.onmessage = (event) => {
            const newGreeting = JSON.parse(event.data);
            setData(currentData => {
                if (currentData.some(item => item.id === newGreeting.id)) {
                    return currentData;
                }
                return [newGreeting, ...currentData];
            });
        };

        return () => {
            eventSource.close();
        };
    }, []);

    return (
        <DataTable
            columns={columns}
            data={data}
            progressPending={loading}
            pagination
            highlightOnHover
            pointerOnHover
        />
    );
}
