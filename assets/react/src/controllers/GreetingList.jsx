import React, { useState, useEffect } from 'react';
import DataTable from 'react-data-table-component';
import axios from 'axios';

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
    const [error, setError] = useState(null);

    useEffect(() => {
        axios.get(endpoint)
            .then(res => {
                setData(res.data);
                setLoading(false);
            })
            .catch(error => {
                console.error("Error fetching data:", error);
                setError('Impossible de charger les salutations.');
                setLoading(false);
            });
    }, [endpoint]);

    if (loading) {
        return <div>Chargement en cours...</div>;
    }

    if (error) {
        return <div style={{ color: 'red' }}>{error}</div>;
    }

    if (data.length === 0) {
        return <div>Aucun message à afficher.</div>;
    }

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
