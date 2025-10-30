import dayjs from 'dayjs'

export function formatIDR(value: number) {
    const formatted = new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value)
    return `Rp. ${formatted}`
}

export function trimWhitespace(value: string){
    return value.trim();
}

// Interface addConfirm
interface confirmInterface {
    group: string;
    header: string;
    message: string;
    icon: string;
    class: string;
    rejectProps: {
        label: string;
    };
    acceptProps: {
        label: string;
    };
    accept: () => void;
    reject: () => void;
}

const defaultConfirmData: confirmInterface = {
    group: 'main',
    header: "Confirmation",
    message: "Are you sure?",
    icon: "pi pi-exclamation-triangle",
    class: "bg-primary",
    rejectProps: {
        label: "No"
    },
    acceptProps: {
        label: "Yes"
    },
    accept: () => {},
    reject: () => {},
};

export function addConfirm(confirm, confirmData: Partial<confirmInterface> = {}) {
    // Merge defaults with the provided data
    const finalData = { ...defaultConfirmData, ...confirmData };
    confirm.require(finalData);
}

export function formatDecimal(value: number) {
    const formatted = new Intl.NumberFormat('en-EN', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    }).format(value)
    return formatted;
}

export function formatDecimalforInputNumber(value: number) {
    const formatted = new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    }).format(value)
    return Number(formatted.replace(/\./g, ''));
}

export function formatDatefromString(date: string) {
    if (!date) return '';
     
    return new Date(date).toLocaleDateString();
}

export function parseNumber(value: string, { locale = 'id-ID', minimumFractionDigits = 0, maximumFractionDigits = 2 } = {}) {
    const parsedNumber = new Intl.NumberFormat(locale, {
        minimumFractionDigits: minimumFractionDigits,
        maximumFractionDigits: maximumFractionDigits,
    }).format(Number(value));
    return parsedNumber;
    
}

export function parseDate(date: string, format: string = 'YYYY-MM-DD HH:mm:ss', { plusDays = 0, plusMonths = 0, plusYears = 0 } = {}) {
    return dayjs(date).add(plusDays, 'day').add(plusMonths, 'month').add(plusYears, 'year').format(format);
}

export function parseDateForDatePicker(date: string){
    return dayjs(date).toDate();
}

export function formatDatefromTimestamp(timestamp) {
    const d = new Date(timestamp);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}/${month}/${day}`;
}

export function formatDateTimefromString(date: string) {
    const dateObj = new Date(date);
  
  // Format date part (yyyy/mm/dd)
  const datePart = new Intl.DateTimeFormat('id-ID', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
  }).format(dateObj);
  
  // Format time part (H:i:s)
  const timePart = new Intl.DateTimeFormat('en-EN', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
    hour12: false
  }).format(dateObj);
  
  return `${datePart} ${timePart}`;
}

export const formatDateTimeForLaravel = (date: Date | string | null): string => {
  if (!date) return '';
  
  const parsedDate = (typeof date === 'string') ? new Date(date) : date;

  if (isNaN(parsedDate.getTime())) {
    console.warn("Invalid date format:", date);
    return '';
  }

  // Formatting to 'YYYY-MM-DD HH:mm:ss' (local time)
  const yyyy = parsedDate.getFullYear();
  const mm = String(parsedDate.getMonth() + 1).padStart(2, '0');
  const dd = String(parsedDate.getDate()).padStart(2, '0');
  const hh = String(parsedDate.getHours()).padStart(2, '0');
  const min = String(parsedDate.getMinutes()).padStart(2, '0');
  const ss = String(parsedDate.getSeconds()).padStart(2, '0');

  return `${yyyy}-${mm}-${dd} ${hh}:${min}:${ss}`;
};

export function timeDifference(date: string | Date, date2: string | Date) {
    const d1 = new Date(date);
    const d2 = new Date(date2);
    const diff = d2.getTime() - d1.getTime();
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const months = Math.floor(days / 30);
    const hour = Math.floor((diff / (1000 * 60 * 60)) % 24);
    const minutes = Math.floor((diff / (1000 * 60)) % 60);
    const second = Math.floor((diff / 1000) % 60);
    return { days, months, hour, minutes, second };
}

export function preventMaxNumber(event, maxValue: number){
    if (event.value > maxValue) {
        event.preventDefault();
    }
}

export function downloadImage(url){
    const link = document.createElement('a');
    link.href = url;
    link.download = url.split('/').pop();
    link.click();
    link.remove();
}

export function openImage(url){
    window.open(url, '_blank');
}

export function capitalize(str) {
  if (!str) return '';
  return str.charAt(0).toUpperCase() + str.slice(1);
}

export async function sleep(ms: number): Promise<void> {
    return new Promise((resolve) => setTimeout(resolve, ms));
}