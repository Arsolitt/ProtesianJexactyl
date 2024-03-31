import { format } from 'date-fns';
import useFlash from '@/plugins/useFlash';
import { useRouteMatch } from 'react-router';
import React, { useEffect, useState } from 'react';
import { Alert } from '@/components/elements/alert';
import { Button } from '@/components/elements/button';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import PageContentBlock from '@/components/elements/PageContentBlock';
import NewMessageDialog from '@/components/tickets/forms/NewMessageDialog';
import { deleteTicket, getMessages, getTicket, Ticket, TicketMessage } from '@/api/account/tickets';
import { ru } from 'date-fns/locale';
import { NotFound } from '@/components/elements/ScreenBlock';

export default () => {
    const { clearFlashes } = useFlash();
    const match = useRouteMatch<{ id: string }>();
    const id = parseInt(match.params.id);

    const [visible, setVisible] = useState(false);
    const [ticket, setTicket] = useState<Ticket>();
    const [messages, setMessages] = useState<TicketMessage[]>();

    const doRedirect = () => {
        clearFlashes('tickets');

        // @ts-expect-error this is valid
        window.location = '/tickets';
    };

    const doRefresh = () => {
        clearFlashes('tickets');

        getTicket(id).then((data) => setTicket(data));

        getMessages(id).then((data) => setMessages(data));
    };

    const doDeletion = () => {
        clearFlashes('tickets');

        deleteTicket(id).then(() => doRedirect());
    };

    useEffect(() => {
        clearFlashes('tickets');

        doRefresh();
    }, []);

    const visibleStatus = (status: string) => {
        if (status === 'pending') {
            return 'В ожидании';
        } else if (status === 'in-progress') {
            return 'В процессе';
        } else if (status === 'resolved') {
            return 'Решено';
        } else if (status === 'unresolved') {
            return 'Не решено';
        } else {
            return 'Неизвестно :(';
        }
    };

    if (!ticket)
        return (
            <NotFound title={'Тикет не найден'} onBack={() => doRedirect()} message={'Кажется, тут ничего нет...'} />
        );

    return (
        <PageContentBlock title={'View Ticket'} showFlashKey={'tickets'}>
            <NewMessageDialog open={visible} onClose={() => setVisible(false)} />
            <div className={'mt-6 grid grid-cols-1 sm:grid-cols-2 lg:w-1/3 gap-4'}>
                <Button.Text className={'w-full'} onClick={doRedirect}>
                    Посмотреть все обращения
                </Button.Text>
                <Button.Danger className={'w-full'} onClick={doDeletion}>
                    Удалить обращение
                </Button.Danger>
            </div>
            <Alert
                type={
                    ticket.status === 'pending'
                        ? 'info'
                        : ticket.status === 'in-progress'
                        ? 'info'
                        : ticket.status === 'unresolved'
                        ? 'danger'
                        : ticket.status === 'resolved'
                        ? 'success'
                        : 'warning'
                }
                className={'my-4 w-full'}
            >
                Обращение находится в статусе: &nbsp;<p className={'font-bold'}>{visibleStatus(ticket.status)}</p>.
            </Alert>
            <TitledGreyBox title={ticket.title}>
                <p className={'line-clamp-5 truncate'}>{ticket.content}</p>
                {ticket.createdAt && (
                    <p className={'text-right p-2 text-sm text-gray-400'}>
                        {format(ticket.createdAt, "MMM do 'в' h:mma", { locale: ru })}
                    </p>
                )}
            </TitledGreyBox>
            {!messages ? (
                <p className={'text-gray-400 text-center'}>Никто ещё не ответил на твоё обращение :(</p>
            ) : (
                <>
                    {messages.map((message) => (
                        <div key={message.id}>
                            {message.content === ticket.content ? undefined : (
                                <>
                                    {message.userEmail === 'system' ? (
                                        <div className={'my-4 p-2 bg-gray-800 opacity-25'}>
                                            <p className={'text-lg text-center text-gray-400'}>{message.content}</p>
                                        </div>
                                    ) : (
                                        <TitledGreyBox title={`Сообщение от: ${message.userEmail}`} className={'mt-4'}>
                                            <p className={'line-clamp-5 truncate'}>{message.content}</p>
                                            {message.createdAt && (
                                                <p className={'text-right p-2 text-sm text-gray-400'}>
                                                    {format(message.createdAt, "MMM do 'в' h:mma", { locale: ru })}
                                                </p>
                                            )}
                                        </TitledGreyBox>
                                    )}
                                </>
                            )}
                        </div>
                    ))}
                </>
            )}
            <div className={'flex justify-center items-center mt-6'}>
                <Button.Success onClick={() => setVisible(true)}>Написать сообщение</Button.Success>
            </div>
        </PageContentBlock>
    );
};
